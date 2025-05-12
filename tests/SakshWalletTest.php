<?php

namespace Saksh\Wallet\Tests;

use Saksh\Wallet\Services\SakshWallet;
use Saksh\Wallet\Models\Balance;
use Saksh\Wallet\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SakshWalletTest extends TestCase
{
    use RefreshDatabase;

    protected SakshWallet $wallet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->wallet = app('saksh-wallet');
    }

    public function test_can_add_funds(): void
    {
        $userId = 1;
        $currency = 'USD';

        $result = $this->wallet->addFunds($userId, 500, $currency, 'ref123', 'Salary payment');
        $this->assertEquals('Credited 500 USD. New balance is 500', $result);

        $balance = Balance::where('user_id', $userId)->where('currency', $currency)->first();
        $this->assertEquals(500, $balance->balance);

        $transaction = WalletTransaction::where('user_id', $userId)->first();
        $this->assertEquals(500, $transaction->amount);
        $this->assertEquals('credit', $transaction->type);
    }

    public function test_can_deduct_funds(): void
    {
        $userId = 1;
        $currency = 'USD';

        $this->wallet->addFunds($userId, 500, $currency, 'ref123', 'Salary payment');
        $result = $this->wallet->deductFunds($userId, 200, $currency, 'ref124', 'Grocery shopping', 5);
        $this->assertEquals('Debited 200 USD. New balance is 295', $result);

        $balance = Balance::where('user_id', $userId)->where('currency', $currency)->first();
        $this->assertEquals(295, $balance->balance);

        $transaction = WalletTransaction::where('user_id', $userId)->where('type', 'debit')->first();
        $this->assertEquals(200, $transaction->amount);
        $this->assertEquals(5, $transaction->fee);
    }

    public function test_get_balance(): void
    {
        $userId = 1;
        $currency = 'USD';

        $this->wallet->addFunds($userId, 500, $currency, 'ref123', 'Salary payment');
        $balance = $this->wallet->getBalance($userId, $currency);
        $this->assertEquals([
            'user_id' => $userId,
            'currency' => $currency,
            'balance' => 500.0,
        ], $balance);
    }

    public function test_get_balance_summary(): void
    {
        $userId = 1;

        $this->wallet->addFunds($userId, 500, 'USD', 'ref123', 'Salary payment');
        $this->wallet->addFunds($userId, 100, 'EUR', 'ref125', 'Bonus payment');
        $summary = $this->wallet->sakshGetBalanceSummary($userId);
        $this->assertEquals([
            'user_id' => $userId,
            'balance' => ['USD' => 500.0, 'EUR' => 100.0],
        ], $summary);
    }

    public function test_insufficient_balance(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient balance');

        $this->wallet->deductFunds(1, 100, 'USD', 'ref125', 'Overdraft');
    }

    public function test_invalid_inputs(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->wallet->addFunds(1, -100, 'USD', 'ref126', 'Invalid credit');
    }
}