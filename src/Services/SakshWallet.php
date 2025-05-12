<?php

namespace Saksh\Wallet\Services;

use Saksh\Wallet\Models\Balance;
use Saksh\Wallet\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class SakshWallet
{
    protected ?string $adminEmail = null;

    public function setAdmin(string $adminEmail): self
    {
        $this->adminEmail = $adminEmail;
        return $this;
    }

    public function addFunds(int $userId, float $amount, string $currency, ?string $reference = null, ?string $description = null): string
    {
        return $this->sakshCredit($userId, $amount, $currency, $reference, $description);
    }

    public function deductFunds(int $userId, float $amount, string $currency, ?string $reference = null, ?string $description = null, float $fee = 0): string
    {
        return $this->sakshDebit($userId, $amount, $currency, $reference, $description, $fee);
    }

    public function sakshCredit(int $userId, float $amount, string $currency, ?string $reference = null, ?string $description = null): string
    {
        $this->validateInputs($amount, $currency, $fee = 0);

        return DB::transaction(function () use ($userId, $amount, $currency, $reference, $description) {
            $balance = $this->getOrCreateBalance($userId, $currency);
            $newBalance = $balance->balance + $amount;

            $balance->update(['balance' => $newBalance]);

            WalletTransaction::create([
                'user_id' => $userId,
                'balance_id' => $balance->id,
                'amount' => $amount,
                'currency' => $currency,
                'reference' => $reference,
                'description' => $description,
                'type' => 'credit',
            ]);

            return "Credited {$amount} {$currency}. New balance is {$newBalance}";
        });
    }

    public function sakshDebit(int $userId, float $amount, string $currency, ?string $reference = null, ?string $description = null, float $fee = 0): string
    {
        $this->validateInputs($amount, $currency, $fee);

        return DB::transaction(function () use ($userId, $amount, $currency, $reference, $description, $fee) {
            $balance = $this->getOrCreateBalance($userId, $currency);
            $totalDeduction = $amount + $fee;

            if ($balance->balance < $totalDeduction) {
                throw new \Exception('Insufficient balance');
            }

            $newBalance = $balance->balance - $totalDeduction;
            $balance->update(['balance' => $newBalance]);

            WalletTransaction::create([
                'user_id' => $userId,
                'balance_id' => $balance->id,
                'amount' => $amount,
                'currency' => $currency,
                'reference' => $reference,
                'description' => $description,
                'fee' => $fee,
                'type' => 'debit',
            ]);

            return "Debited {$amount} {$currency}. New balance is {$newBalance}";
        });
    }

    public function getBalance(int $userId, string $currency): array
    {
        $balance = $this->getOrCreateBalance($userId, $currency);
        return [
            'user_id' => $userId,
            'currency' => $currency,
            'balance' => $balance->balance,
        ];
    }

    public function sakshGetBalance(int $userId, string $currency): array
    {
        return $this->getBalance($userId, $currency);
    }

    public function sakshGetBalanceSummary(int $userId): array
    {
        $balances = Balance::where('user_id', $userId)
            ->pluck('balance', 'currency')
            ->toArray();

        return [
            'user_id' => $userId,
            'balance' => $balances,
        ];
    }

    protected function getOrCreateBalance(int $userId, string $currency): Balance
    {
        return Balance::lockForUpdate()->firstOrCreate(
            ['user_id' => $userId, 'currency' => $currency],
            ['balance' => 0]
        );
    }

    protected function validateInputs(float $amount, string $currency, float $fee): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
        if ($fee < 0) {
            throw new \InvalidArgumentException('Fee cannot be negative');
        }
        if (strlen($currency) !== 3) {
            throw new \InvalidArgumentException('Currency must be a 3-letter code');
        }
    }
}