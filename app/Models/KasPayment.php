<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasPayment extends Model
{
    use HasFactory;

    public function data_warga()
    {
        return $this->belongsTo(DataWarga::class, 'data_warga_id');
    }
    public function submitted()
    {
        return $this->belongsTo(DataWarga::class, 'submitted_by');
    }
    public function confirmed()
    {
        return $this->belongsTo(DataWarga::class, 'confirmed_by');
    }
}
