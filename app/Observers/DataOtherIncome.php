<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\OtherIncomes;

class DataOtherIncome
{
    /**
     * Handle the OtherIncome "created" event.
     */
    public function created(OtherIncomes $otherIncome): void
    {
        ActivityLog::create([
            'code' => $otherIncome->code,
            'action' => 'create',
            'model' => 'otherIncome',
            'details' => $otherIncome->submitted->name . ' created other Income: ' . $otherIncome->amount,
        ]);
    }

    /**
     * Handle the OtherIncomes "updated" event.
     */
    public function updated(OtherIncomes $otherIncome): void
    {
        // Ambil data yang lama dan baru
        $original = $otherIncome->getOriginal();
        $changed = $otherIncome->getChanges();

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
                'code' => $otherIncome->code,
                'action' => 'update',
                'model' => 'otherIncome',
                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the OtherIncomes "deleted" event.
     */
    public function deleted(OtherIncomes $otherIncome): void
    {
        ActivityLog::create([
            'code' => $otherIncome->code,
            'action' => 'delete',
            'model' => 'otherIncome',
            'details' => $otherIncome->submitted->name . ' Delete other Income: ' . $otherIncome->amount,
        ]);
    }

    /**
     * Handle the OtherIncomes "restored" event.
     */
    public function restored(OtherIncomes $otherIncome): void
    {
        //
    }

    /**
     * Handle the OtherIncomes "force deleted" event.
     */
    public function forceDeleted(OtherIncomes $otherIncome): void
    {
        //
    }
}
