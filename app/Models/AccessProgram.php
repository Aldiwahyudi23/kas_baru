<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessProgram extends Model
{
    use HasFactory;

    // Add the fillable properties
    protected $fillable = [
        'data_warga_id',
        'program_id',
        'is_active',
    ];

    public function dataWarga()
    {
        return $this->belongsTo(DataWarga::class, 'data_warga_id');
    }
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
