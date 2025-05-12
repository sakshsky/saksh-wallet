# Saksh Wallet

A Laravel package for managing user wallets and transactions.

## Installation

```bash
composer require saksh/wallet
```

## Usage

1. Publish the migrations:
```bash
php artisan vendor:publish --provider="Saksh\Wallet\SakshWalletServiceProvider"
```

2. Run the migrations:
```bash
php artisan migrate
```

## Commands

- Verify wallet integrity:
```bash
php artisan wallet:verify
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
