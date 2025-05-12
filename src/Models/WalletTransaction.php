<?php

namespace Saksh\Wallet\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'balance_id',
        'amount',
        'currency',
        'reference',
        'description',
        'fee',
        'type',
    ];

    public function balance()
    {
        return $this->belongsTo(Balance::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}