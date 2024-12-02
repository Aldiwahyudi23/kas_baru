<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositDetail extends Model
{
    use HasFactory;

    public function deposit()
    {
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }
}
