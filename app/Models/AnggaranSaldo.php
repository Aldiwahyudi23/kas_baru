<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggaranSaldo extends Model
{
    use HasFactory;

    public function saldos()
    {
        return $this->belongsTo(Saldo::class, 'saldo_id');
    }
}
