<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'member_type_id', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function memberType()
    {
        return $this->belongsTo(MemberType::class);
    }
}