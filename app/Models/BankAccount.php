<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
      use HasFactory, SoftDeletes;

    protected $fillable = [
        'warga_id',
        'bank_name',
        'account_number',
        'account_holder_name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi ke Warga
    public function warga()
    {
        return $this->belongsTo(DataWarga::class);
    }

    // Relasi ke Saldo
    public function saldo()
    {
        return $this->belongsTo(Saldo::class);
    }

    // Scope untuk akun aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function latestBalance()
{
    return $this->hasOne(BankTransaction::class)->latestOfMany();
}
}
