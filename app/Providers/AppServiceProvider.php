<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        if (env('APP_DEBUG')) {
            error_reporting(E_ALL & ~E_DEPRECATED);
        }
        if (config('app.https_enabled')) {
            \URL::forceScheme('https');
        }

        // If mass assignment disabled
        if (Model::isUnguarded()) {
            // Enable all mass assignable restrictions.
            Model::reguard();
        }
    }
}
