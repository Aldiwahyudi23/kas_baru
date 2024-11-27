<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'action',
        'model',
        'admin_id',
        'details',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
