<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggaranSetting extends Model
{
    use HasFactory;

    public function anggaran()
    {
        return $this->belongsTo(Anggaran::class, 'anggaran_id');
    }
}
