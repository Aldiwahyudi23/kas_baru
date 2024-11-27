<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    public function anggaran()
    {
        return $this->belongsTo(Anggaran::class, 'anggaran_id');
    }
    public function warga()
    {
        return $this->belongsTo(DataWarga::class, 'data_warga_id');
    }
    public function sekretaris()
    {
        return $this->belongsTo(DataWarga::class, 'submitted_by');
    }
    public function ketua()
    {
        return $this->belongsTo(DataWarga::class, 'approved_by');
    }
    public function bendahara()
    {
        return $this->belongsTo(DataWarga::class, 'disbursed_by');
    }
}
