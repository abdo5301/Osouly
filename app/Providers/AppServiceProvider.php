<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Auth\DBSessionAuth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        set_time_limit(0);
        Schema::defaultStringLength(191);

        // DBSessionAuth Auth Provider
        Auth::extend('DBSessionAuth', function($app,$name, array $config) {
            $providerData = config('auth.providers.'.$config['provider']);
            return new DBSessionAuth($providerData['model'],$name);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
