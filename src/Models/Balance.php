<?php

namespace Saksh\Wallet\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = [
        'user_id',
        'currency',
        'balance',
    ];

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}