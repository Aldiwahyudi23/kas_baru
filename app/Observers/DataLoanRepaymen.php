<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Loan;
use App\Models\LoanRepayment;

class DataLoanRepaymen
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(LoanRepayment $loanRepayment): void
    {
        $loan = Loan::find($loanRepayment->loan_id);
        ActivityLog::create([
            'code' => $loan->code,
            'action' => 'create',
            'model' => 'LoanRepayment',
            'details' => $loanRepayment->submitted->name . ' created LoanRepayment : ' . $loanRepayment->data_warga->name,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(LoanRepayment $loanRepayment): void
    {
        // Ambil data yang lama dan baru
        $original = $loanRepayment->getOriginal();
        $changed = $loanRepayment->getChanges();

        // Daftar atribut yang ingin dikecualikan
        $excludedAttributes = ['updated_at'];

        foreach ($changed as $key => $value) {
            // Lewati atribut yang dikecualikan
            if (in_array($key, $excludedAttributes)) {
                continue;
            }

            // Buat deskripsi perubahan
            $description = "Updated {$key} from {$original[$key]} to {$value}";

            $loan = Loan::find($loanRepayment->loan_id);

            ActivityLog::create([
                'code' => $loan->code,
                'action' => 'update',
                'model' => 'LoanRepayment',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(LoanRepayment $loanRepayment): void
    {
        $loan = Loan::find($loanRepayment->loan_id);
        ActivityLog::create([
            'code' => $loan->code,
            'action' => 'delet',
            'model' => 'LoanRepayment',
            'details' => $loanRepayment->submitted->name . ' created LoanRepayment : ' . $loanRepayment->data_warga->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(LoanRepayment $loanRepayment): void
    {
        $loan = Loan::find($loanRepayment->loan_id);
        ActivityLog::create([
            'code' => $loan->code,
            'action' => 'restore',
            'model' => 'LoanRepayment',
            'details' => $loanRepayment->submitted->name . ' created Expenditure : ' . $loanRepayment->data_warga->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(LoanRepayment $loanRepayment): void
    {
        //
    }
}
