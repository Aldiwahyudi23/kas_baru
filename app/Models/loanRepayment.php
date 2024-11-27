<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class loanRepayment extends Model
{
    use HasFactory;

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
    public function data_warga()
    {
        return $this->belongsTo(DataWarga::class, 'data_warga_id');
    }
    public function submitted()
    {
        return $this->belongsTo(DataWarga::class, 'submitted_by');
    }
    public function confirmed()
    {
        return $this->belongsTo(DataWarga::class, 'confirmed_by');
    }
}
