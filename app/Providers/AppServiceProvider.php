<?php

namespace App\Providers;

use App\Models\Calculation;
use App\Models\Contact;
use App\Models\Followup;
use App\Models\Lead;
use App\Models\Transaction;
use App\Models\User;
use App\Policies\CalculationPolicy;
use App\Policies\ContactPolicy;
use App\Policies\FollowupPolicy;
use App\Policies\LeadPolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('is-master', function (User $user) {
            return $user->isMaster();
        });

        Gate::policy(Contact::class, ContactPolicy::class);
        Gate::policy(Calculation::class, CalculationPolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);
        Gate::policy(Followup::class, FollowupPolicy::class);
        Gate::policy(Lead::class, LeadPolicy::class);
    }
}
