<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataNotification extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'wa_notification', 'email_notification', 'pengurus', 'anggota', 'program'];

    public function accessNotifications()
    {
        return $this->hasMany(AccessNotification::class, 'notification_id');
    }
}
