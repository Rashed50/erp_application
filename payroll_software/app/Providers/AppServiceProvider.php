<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
    public function boot(){

        if ($this->app->runningInConsole()) {
            \URL::forceRootUrl(config('app.url'));
        }
        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
        else if($this->app->environment('local')) {
            \URL::forceScheme('http');
        }
        Paginator::useBootstrap();
    }
}
