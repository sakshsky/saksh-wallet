<?php

namespace Saksh\Wallet\Console;

use Saksh\Wallet\Models\WalletTransaction;
use Saksh\Wallet\Models\Balance;
use Illuminate\Console\Command;

class VerifyWalletIntegrity extends Command
{
    protected $signature = 'saksh-wallet:verify';
    protected $description = 'Verify wallet balances against transactions';

    public function handle()
    {
        $this->info('Verifying wallet integrity...');

        $users = Balance::select('user_id')->distinct()->pluck('user_id');
        foreach ($users as $userId) {
            $currencies = Balance::where('user_id', $userId)->pluck('currency');
            foreach ($currencies as $currency) {
                $transactions = WalletTransaction::where('user_id', $userId)
                    ->where('currency', $currency)
                    ->orderBy('id')
                    ->get();

                $calculatedBalance = 0;
                foreach ($transactions as $transaction) {
                    $calculatedBalance += $transaction->type === 'credit'
                        ? $transaction->amount
                        : -($transaction->amount + $transaction->fee);
                }

                $storedBalance = Balance::where('user_id', $userId)
                    ->where('currency', $currency)
                    ->first()->balance;

                if ($calculatedBalance != $storedBalance) {
                    $this->error("Mismatch for user ID {$userId} ({$currency}): Stored: {$storedBalance}, Calculated: {$calculatedBalance}");
                }
            }
        }
        $this->info('Wallet verification complete.');
    }
}