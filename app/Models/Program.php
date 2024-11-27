<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    public function program_setting()
    {
        return $this->belongsTo(ProgramSetting::class);
    }

    public function dataWarga()
    {
        return $this->belongsToMany(DataWarga::class, 'access_programs')
            ->withPivot('is_active')
            ->wherePivot('is_active', 1); // Menampilkan warga yang aktif
    }
}
