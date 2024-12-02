<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    public function submit()
    {
        return $this->belongsTo(DataWarga::class, 'submitted_by');
    }
    public function confirm()
    {
        return $this->belongsTo(DataWarga::class, 'confirmed_by');
    }
    public function details()
    {
        return $this->hasMany(DepositDetail::class);
    }
}
