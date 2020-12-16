<?php

namespace App\Providers;

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
        $env = config('app.env');

        switch ($env) {
            case 'prod':
            case 'production':
            case 'test':
                \URL::forceScheme('https');
                break;
        }
    }
}
