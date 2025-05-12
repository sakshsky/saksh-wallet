# Saksh Wallet for Laravel

A simple and performant wallet system for Laravel applications, using two tables to manage transactions and balances.

## Installation

1. Install via Composer:
   ```bash
   composer require sakshsky/saksh-wallet
   ```

2. Publish and run migrations:
   ```bash
   php artisan vendor:publish --tag=saksh-wallet-migrations
   php artisan migrate
   ```
   This creates two tables (`wallet_transactions` and `balances`) via a single migration.

## Usage

```php
use Saksh\Wallet\Services\SakshWallet;

$wallet = app('saksh-wallet');
$userId = 1;

// Set admin email (optional)
$wallet->setAdmin('admin@example.com');

// Credit an amount
echo $wallet->addFunds($userId, 500, 'USD', 'ref123', 'Salary payment');
// Output: Credited 500 USD. New balance is 500

// Debit an amount with a fee
echo $wallet->deductFunds($userId, 200, 'USD', 'ref124', 'Grocery shopping', 5);
// Output: Debited 200 USD. New balance is 295

// Get balance
$balance = $wallet->getBalance($userId, 'USD');
print_r($balance);
// Output: ['user_id' => 1, 'currency' => 'USD', 'balance' => 295]

// Get balance summary
$summary = $wallet->sakshGetBalanceSummary($userId);
print_r($summary);
// Output: ['user_id' => 1, 'balance' => ['USD' => 295]]
```

## Database Structure

- **`wallet_transactions`**: Stores transaction history (credits and debits).
- **`balances`**: Stores current balances for each user and currency.

## Commands

- Verify wallet integrity:
  ```bash
  php artisan saksh-wallet:verify
  ```

## Testing

Run the tests:
```bash
vendor/bin/phpunit
```

## Contributing
Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Repository
Find the source code at [https://github.com/sakshsky/saksh-wallet](https://github.com/sakshsky/saksh-wallet).

## License
This package is open-sourced software licensed under the [MIT license](LICENSE).