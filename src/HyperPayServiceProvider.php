<?php

namespace maree\hyperPay;

use Illuminate\Support\ServiceProvider;

class HyperPayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__.'/config/hyperPay.php' => config_path('hyperPay.php'),
        ],'hyperPay');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/hyperPay.php', 'hyperPay'
        );
    }
}
