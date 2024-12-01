<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanExtension extends Model
{
    use HasFactory;

    public function pinjaman()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
    public function data_warga()
    {
        return $this->belongsTo(DataWarga::class, 'submitted_by');
    }
}
