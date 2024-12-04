<?php

namespace App\Models\Konter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKonter extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(ProductKonter::class, 'product_id');
    }
}
