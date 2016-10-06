<?php

namespace App\Providers;

use CustomUserProvider;

use Illuminate\Support\ServiceProvider;

class CustomAuthProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->app['auth']->provider('custom',function()
        {

            return new \App\Providers\CustomUserProvider();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
