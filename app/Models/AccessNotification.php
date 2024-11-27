<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessNotification extends Model
{
    use HasFactory;

    public function dataWarga()
    {
        return $this->belongsTo(DataWarga::class, 'data_warga_id');
    }
}
