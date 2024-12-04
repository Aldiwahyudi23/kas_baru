<?php

namespace App\Models\Konter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductKonter extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_id',
        'provider_id',
        'amount',
        'buying_price',
        'price',
        'price1',
        'price2',
        'price3',
        'price4',
    ];
    public function kategori()
    {
        return $this->belongsTo(KategoriKonter::class, 'kategori_id');
    }

    public function provider()
    {
        return $this->belongsTo(ProviderKonter::class, 'provider_id');
    }

    public function transaksi()
    {
        return $this->hasMany(TransaksiKonter::class);
    }
}
