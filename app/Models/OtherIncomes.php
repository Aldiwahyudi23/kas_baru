<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherIncomes extends Model
{
    use HasFactory;

    public function anggaran()
    {
        return $this->belongsTo(Anggaran::class, 'anggaran_id');
    }
    public function submitted()
    {
        return $this->belongsTo(DataWarga::class, 'submitted_by');
    }
    public function confirmed()
    {
        return $this->belongsTo(DataWarga::class, 'confirmed_by');
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }
}
