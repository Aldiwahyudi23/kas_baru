<?php

namespace App\Models\Konter;

use App\Models\DataWarga;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKonter extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'product_id',
        'konter_detail_id',
        'submitted_by',
        'payment_status',
        'payment_method',
        'status',
        'buying_price',
        'price',
        'diskon',
        'invoice',
        'margin',
        'deadline_date',
        'is_deposited',
        'deposit_id',
        'warga_id',
        'confirmed_by',
        'confirmation_date',
    ];

    public function product()
    {
        return $this->belongsTo(ProductKonter::class, 'product_id');
    }
    public function detail()
    {
        return $this->belongsTo(DetailTransaksiKonter::class, 'konter_detail_id');
    }
    public function warga()
    {
        return $this->belongsTo(DataWarga::class, 'warga_id');
    }
    public function confirmed()
    {
        return $this->belongsTo(DataWarga::class, 'confirmed_by');
    }
}