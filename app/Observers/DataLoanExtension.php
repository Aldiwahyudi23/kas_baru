<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Loan;
use App\Models\LoanExtension;

class DataLoanExtension
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(LoanExtension $loanExtension): void
    {
        $loan = Loan::find($loanExtension->loan_id);
        ActivityLog::create([
            'code' => $loan->code,
            'action' => 'create',
            'model' => 'LoanExtension',
            'details' => $loanExtension->data_warga->name . ' created LoanExtension : ' . $loan->warga->name,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(LoanExtension $loanExtension): void
    {
        // Ambil data yang lama dan baru
        $original = $loanExtension->getOriginal();
        $changed = $loanExtension->getChanges();

        // Daftar atribut yang ingin dikecualikan
        $excludedAttributes = ['updated_at'];

        foreach ($changed as $key => $value) {
            // Lewati atribut yang dikecualikan
            if (in_array($key, $excludedAttributes)) {
                continue;
            }

            // Buat deskripsi perubahan
            $description = "Updated {$key} from {$original[$key]} to {$value}";

            $loan = Loan::find($loanExtension->loan_id);
            ActivityLog::create([
                'code' => $loan->code,
                'action' => 'update',
                'model' => 'LoanExtension',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(LoanExtension $loanExtension): void
    {
        $loan = Loan::find($loanExtension->loan_id);
        ActivityLog::create([
            'code' => $loan->code,
            'action' => 'delet',
            'model' => 'LoanExtension',
            'details' => $loanExtension->data_warga->name . ' created LoanExtension : ' . $loan->warga->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(LoanExtension $loanExtension): void
    {
        $loan = Loan::find($loanExtension->loan_id);
        ActivityLog::create([
            'code' => $loan->code,
            'action' => 'restore',
            'model' => 'LoanExtension',
            'details' => $loanExtension->data_warga->name . ' created Expenditure : ' . $loan->warga->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(LoanExtension $loanExtension): void
    {
        //
    }
}
