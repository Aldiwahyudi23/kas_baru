<?php

namespace App\Models\Konter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKonter extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'product_id',
        'submitted_by',
        'payment_method',
        'status',
        'buying_price',
        'price',
        'is_deposited',
        'deposit_id',
        'deadline_date',
    ];

    public function product()
    {
        return $this->belongsTo(ProductKonter::class, 'product_id');
    }
}