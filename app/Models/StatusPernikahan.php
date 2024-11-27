<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPernikahan extends Model
{
    use HasFactory;

    protected $table = 'status_pernikahans';
    public function dataWarga()
    {
        return $this->belongsTo(DataWarga::class);
    }
    public function suami()
    {
        return $this->belongsTo(DataWarga::class, 'warga_suami_id', 'id', 'status', 'name');
    }

    public function istri()
    {
        return $this->belongsTo(DataWarga::class, 'warga_istri_id', 'id', 'status', 'name');
    }
}
