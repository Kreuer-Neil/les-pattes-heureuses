<?php

namespace App\Providers;

use App\Models\AdopterProfile;
use App\Models\AdoptionRequest;
use App\Models\Animal;
use App\Models\User;
use App\Policies\AdopterProfilePolicy;
use App\Policies\AdoptionRequestPolicy;
use App\Policies\AnimalPolicy;
use App\Policies\UserPolicy;
use Gate;
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
        Gate::policy(Animal::class, AnimalPolicy::class);
        Gate::policy(AdoptionRequest::class, AdoptionRequestPolicy::class);
        Gate::policy(AdopterProfile::class, AdopterProfilePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
