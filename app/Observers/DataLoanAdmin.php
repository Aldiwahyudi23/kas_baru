<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Loan;

class DataLoanAdmin

{
    /**
     * Handle the Admin "created" event.
     */
    public function created(Loan $loan): void
    {
        ActivityLog::create([
            'code' => $loan->code,
            'action' => 'create',
            'model' => 'Loan',
            'details' => $loan->sekretaris->name . ' created Loan : ' . $loan->warga->name . '(' . number_format($loan->loan_amount, 0, ',', '.') . ')',
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(Loan $loan): void
    {
        // Ambil data yang lama dan baru
        $original = $loan->getOriginal();
        $changed = $loan->getChanges();

        // Daftar atribut yang ingin dikecualikan
        $excludedAttributes = ['updated_at'];

        foreach ($changed as $key => $value) {
            // Lewati atribut yang dikecualikan
            if (in_array($key, $excludedAttributes)) {
                continue;
            }

            // Buat deskripsi perubahan
            $description = "Updated {$key} from {$original[$key]} to {$value}";


            ActivityLog::create([
                'code' => $loan->code,
                'action' => 'update',
                'model' => 'Loan',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(Loan $loan): void
    {
        ActivityLog::create([
            'code' => $loan->code,
            'action' => 'delet',
            'model' => 'Loan',
            'details' => $loan->sekretaris->name . ' created Expenditure : ' . $loan->anggaran->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(Loan $loan): void
    {
        ActivityLog::create([
            'code' => $loan->code,
            'action' => 'restore',
            'model' => 'Loan',
            'details' => $loan->sekretaris->name . ' created Expenditure : ' . $loan->anggaran->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(Loan $loan): void
    {
        //
    }
}
