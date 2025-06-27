<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
        
        Gate::define('access-filament', function ($user) {
            if ($user->role === 'admin') {
                return true;
            }
            throw new  AccessDeniedHttpException('You are not authorized to access this page.');
        });
        Gate::define('cashier-dashboard', function ($user) { 
            if($user->role === 'cashier'){
                return true;
            }
            throw new  AccessDeniedHttpException('You are not authorized to access this page.');
        });
    }
}
