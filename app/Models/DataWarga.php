<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataWarga extends Model
{
    public function pernikahanSebagaiSuami()
    {
        return $this->hasMany(StatusPernikahan::class, 'warga_suami_id');
    }

    public function pernikahanSebagaiIstri()
    {
        return $this->hasMany(StatusPernikahan::class, 'warga_istri_id');
    }
    public function statusPernikahan()
    {
        return $this->hasOne(StatusPernikahan::class);
    }

    public function programs()
    {
        return $this->belongsToMany(Program::class, 'access_programs')
            ->withPivot('is_active')
            ->wherePivot('is_active', 1); // Menampilkan program yang aktif
    }
}
