<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Policies\V1\TicketPolicy;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
