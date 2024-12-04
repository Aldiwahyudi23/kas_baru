<?php

namespace App\Models\Konter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriKonter extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', // Add this line
        'name',
        'description',
    ];

    public function product()
    {
        return $this->hasMany(ProductKonter::class);
    }
}
