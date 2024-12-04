<?php

namespace App\Models\Konter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderKonter extends Model
{
    use HasFactory;

    protected $fillable = ['kategori_id', 'name', 'description'];

    public function kategori()
    {
        return $this->belongsTo(KategoriKonter::class, 'kategori_id');
    }
    public function product()
    {
        return $this->hasMany(ProductKonter::class);
    }
}
