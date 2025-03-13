<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\User\UserRepositoryImplementation;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register() {
        $this->app->bind(UserRepositoryInterface::class, UserRepositoryImplementation::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });
    }
}
