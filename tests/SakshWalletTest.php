<?php

namespace Saksh\Wallet\Tests;

use Saksh\Wallet\Services\SakshWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SakshWalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_funds()
    {
        $wallet = new SakshWallet();
        // Add test logic
        $this->assertTrue(true);
    }
}
