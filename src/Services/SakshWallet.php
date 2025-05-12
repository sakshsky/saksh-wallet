<?php

namespace Saksh\Wallet\Services;

use Saksh\Wallet\Models\Balance;
use Saksh\Wallet\Models\WalletTransaction;

class SakshWallet
{
    public function addFunds($userId, $amount, $description = null)
    {
        // Logic to add funds
    }

    public function deductFunds($userId, $amount, $description = null)
    {
        // Logic to deduct funds
    }

    public function getBalance($userId)
    {
        return Balance::where('user_id', $userId)->first();
    }
}
