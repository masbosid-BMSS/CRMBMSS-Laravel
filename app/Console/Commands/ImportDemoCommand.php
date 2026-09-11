<?php

namespace App\Console\Commands;

use Database\Seeders\DemoSeeder;
use Illuminate\Console\Command;

class ImportDemoCommand extends Command
{
    protected $signature = 'bmss:import-demo {--fresh : Migrate fresh database sebelum seeder demo}';
    protected $description = 'Import dan inisialisasi dataset demo realistis untuk CRM BMSS';

    public function handle(): int
    {
        $this->info('Memulai import dataset demo CRM BMSS...');

        if ($this->option('fresh')) {
            $this->call('migrate:fresh', ['--force' => true]);
        }

        $this->call('db:seed', [
            '--class' => DemoSeeder::class,
            '--force' => true,
        ]);

        $this->info('✓ Dataset demo CRM BMSS berhasil diimport!');
        $this->table(['Akun', 'Username', 'Password'], [
            ['Master Admin', 'bmssmanfaat', 'bismillah100'],
            ['CS Nisa', 'nisa.cs', 'admin12345'],
            ['CS Rani', 'rani.cs', 'admin12345'],
            ['CS Fikri', 'fikri.cs', 'admin12345'],
            ['CS Dina', 'dina.cs', 'admin12345'],
            ['CS Yusuf', 'yusuf.cs', 'admin12345'],
        ]);

        return Command::SUCCESS;
    }
}
