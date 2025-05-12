<?php

namespace Saksh\Wallet;

use Illuminate\Support\ServiceProvider;
use Saksh\Wallet\Services\SakshWallet;

class SakshWalletServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SakshWallet::class, function ($app) {
            return new SakshWallet();
        });
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->commands([
            Console\VerifyWalletIntegrity::class,
        ]);
    }
}
