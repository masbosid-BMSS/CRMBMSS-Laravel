<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. WhatsApp Accounts (Maks 5 per CS)
        Schema::create('wa_accounts', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('user_id', 36)->index();
            $table->unsignedTinyInteger('slot'); // 1 - 5
            $table->string('label', 50)->default('WA 1');
            $table->string('phone', 30)->nullable();
            $table->unsignedInteger('capacity')->default(5000);
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'slot']);
        });

        // 2. Programs (Global)
        Schema::create('programs', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('name')->unique();
            $table->string('category', 50)->default('Sedekah'); // Zakat, Infak, Sedekah, Wakaf, dll
            $table->text('description')->nullable();
            $table->enum('status', ['Aktif', 'Arsip'])->default('Aktif');
            $table->timestamps();
        });

        // 3. Campaigns (Global)
        Schema::create('campaigns', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('name');
            $table->string('program_id', 36)->nullable();
            $table->decimal('target_amount', 15, 2)->default(0);
            $table->decimal('achieved_amount', 15, 2)->default(0);
            $table->unsignedInteger('donors_count')->default(0);
            $table->unsignedInteger('new_donors_count')->default(0);
            $table->integer('days_remaining')->default(30);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_global')->default(true);
            $table->enum('status', ['Aktif', 'Selesai', 'Arsip'])->default('Aktif');
            $table->text('description')->nullable();
            $table->string('accent_color', 20)->default('navy');
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->nullOnDelete();
        });

        // 4. Payment Methods (Master Only)
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('name')->unique();
            $table->string('category', 50)->default('Bank Transfer');
            $table->string('detail')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Contacts (Database Donatur)
        Schema::create('contacts', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('niss', 30)->unique()->index();
            $table->string('name')->index();
            $table->string('phone', 30)->index();
            $table->string('city', 100)->nullable()->index();
            $table->string('owner_id', 36)->index();
            $table->string('wa_account_id', 36)->nullable()->index();
            $table->enum('status', ['Baru', 'Aktif', 'Loyal', 'At Risk', 'Dormant'])->default('Baru')->index();
            $table->enum('relation_status', ['Normal', 'Blokir', 'Untrust', 'Bosan'])->default('Normal')->index();
            $table->text('relationship_note')->nullable();
            $table->enum('zakat_status', ['Belum Dihitung', 'Prospek', 'Outstanding', 'Sebagian', 'Lunas', 'Belum Wajib'])->default('Belum Dihitung')->index();
            $table->decimal('ltv', 15, 2)->default(0)->index();
            $table->string('program')->nullable();
            $table->string('source', 100)->nullable();
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->boolean('is_archived')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('wa_account_id')->references('id')->on('wa_accounts')->nullOnDelete();
        });

        // Sequence counter table for NISS generator race condition prevention
        Schema::create('niss_sequences', function (Blueprint $table) {
            $table->string('name', 20)->primary();
            $table->unsignedBigInteger('current_value')->default(0);
            $table->timestamps();
        });

        // 6. Calculations (Zakat Records)
        Schema::create('calculations', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('ref_no', 30)->unique()->index();
            $table->string('contact_id', 36)->index();
            $table->string('owner_id', 36)->index();
            $table->string('type', 50)->default('Zakat Maal');
            $table->decimal('assets_total', 15, 2)->default(0);
            $table->decimal('deductions_total', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->decimal('nisab_amount', 15, 2)->default(0);
            $table->decimal('rate', 8, 6)->default(0.025);
            $table->decimal('zakat_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('haul_status', ['yes', 'no', 'na'])->default('yes');
            $table->enum('status', ['Outstanding', 'Sebagian', 'Lunas', 'Belum Wajib'])->default('Belum Wajib')->index();
            $table->date('calculation_date')->index();
            $table->json('items')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('contacts')->cascadeOnDelete();
            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // 7. Transactions
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('id', 36)->primary(); // e.g. TRX-92818
            $table->string('contact_id', 36)->index();
            $table->string('owner_id', 36)->index();
            $table->string('recorded_by_id', 36)->index();
            $table->enum('type', ['Zakat', 'Infak', 'Sedekah', 'Wakaf', 'Lainnya'])->default('Infak')->index();
            $table->string('program', 100)->nullable()->index();
            $table->string('campaign_id', 36)->nullable()->index();
            $table->string('calculation_id', 36)->nullable()->index();
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('status', ['Paid', 'Pending', 'Failed'])->default('Paid')->index();
            $table->string('payment_method', 100)->nullable();
            $table->timestamp('transaction_date')->useCurrent()->index();
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('contacts')->cascadeOnDelete();
            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('recorded_by_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('campaign_id')->references('id')->on('campaigns')->nullOnDelete();
            $table->foreign('calculation_id')->references('id')->on('calculations')->nullOnDelete();
        });

        // 8. Leads / Pipeline
        Schema::create('leads', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('contact_id', 36)->nullable()->index();
            $table->string('name')->index();
            $table->string('phone', 30)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('source', 100)->nullable();
            $table->string('owner_id', 36)->index();
            $table->enum('stage', ['Lead Baru', 'Contacted', 'Interested', 'Follow-up', 'Donasi'])->default('Lead Baru')->index();
            $table->string('interest', 100)->nullable();
            $table->decimal('potential_amount', 15, 2)->default(0);
            $table->enum('status', ['Aktif', 'Donasi', 'Lost'])->default('Aktif')->index();
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('contacts')->nullOnDelete();
            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // 9. Follow-ups
        Schema::create('followups', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('contact_id', 36)->index();
            $table->string('owner_id', 36)->index();
            $table->string('reason', 50)->default('Relationship'); // Zakat, Program Update, Repeat Donation, Retention, Relationship, Kontak Baru
            $table->string('title');
            $table->enum('priority', ['Normal', 'Tinggi', 'Rendah'])->default('Normal');
            $table->dateTime('scheduled_at')->index();
            $table->enum('status', ['Today', 'Scheduled', 'Overdue', 'Completed'])->default('Scheduled')->index();
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('contacts')->cascadeOnDelete();
            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // 10. KPI Targets per CS
        Schema::create('kpi_targets', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('user_id', 36)->unique()->index();
            $table->date('start_date')->nullable();
            $table->unsignedTinyInteger('tier')->default(1); // 1, 2, 3
            $table->decimal('daily_target', 15, 2)->default(1000000);
            $table->unsignedInteger('followup_target')->default(150);
            $table->decimal('conversion_target', 5, 2)->default(8.00);
            $table->decimal('retention_target', 5, 2)->default(60.00);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        // 11. Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 36)->nullable()->index();
            $table->string('subject_type', 100)->nullable();
            $table->string('subject_id', 36)->nullable();
            $table->string('action', 50); // created, updated, deleted, transferred, etc.
            $table->string('description');
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        // 12. Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 50)->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('kpi_targets');
        Schema::dropIfExists('followups');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('calculations');
        Schema::dropIfExists('niss_sequences');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('wa_accounts');
    }
};
