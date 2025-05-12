<?php

namespace Saksh\Wallet\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = ['user_id', 'amount'];

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
