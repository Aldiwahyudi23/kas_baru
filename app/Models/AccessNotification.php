<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessNotification extends Model
{
    use HasFactory;

    protected $fillable = ['notification_id', 'data_warga_id', 'is_active'];
    public function Warga()
    {
        return $this->belongsTo(DataWarga::class, 'data_warga_id');
    }
    public function notification()
    {
        return $this->belongsTo(DataWarga::class, 'notification_id');
    }
}
