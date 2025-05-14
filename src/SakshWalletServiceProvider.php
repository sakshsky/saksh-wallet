<?php

namespace Saksh\Wallet;

use Illuminate\Support\ServiceProvider;
use Saksh\Wallet\Services\SakshWallet;

class SakshWalletServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'saksh-wallet-migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Saksh\Wallet\Console\VerifyWalletIntegrity::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->app->singleton('saksh-wallet', function () {
            return new SakshWallet();
        });
    }
}