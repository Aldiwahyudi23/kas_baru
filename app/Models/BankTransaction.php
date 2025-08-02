<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{

     use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank_account_id',
        'saldo_id',
        'balance',
        'description'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'balance' => 'decimal:2',
    ];


        public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    /**
     * Get the saldo associated with the transaction.
     */
    public function saldo()
    {
        return $this->belongsTo(Saldo::class);
    }

    /**
     * Format balance as currency.
     *
     * @return string
     */
    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format($this->balance, 2, ',', '.');
    }

    /**
     * Scope a query to filter by bank account.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $bankAccountId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForBankAccount($query, $bankAccountId)
    {
        return $query->where('bank_account_id', $bankAccountId);
    }

    /**
     * Scope a query to filter by saldo.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $saldoId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSaldo($query, $saldoId)
    {
        return $query->where('saldo_id', $saldoId);
    }
}
