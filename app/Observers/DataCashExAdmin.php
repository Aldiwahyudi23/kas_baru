<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\CashExpenditures;
use Illuminate\Support\Facades\Auth;

class DataCashExAdmin

{
    /**
     * Handle the Admin "created" event.
     */
    public function created(CashExpenditures $cashExpenditures): void
    {
        ActivityLog::create([
            'code' => $cashExpenditures->code,
            'action' => 'create',
            'model' => 'CashEx',
            'details' => $cashExpenditures->sekretaris->name . ' created Expenditure : ' . $cashExpenditures->anggaran->name,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(CashExpenditures $cashExpenditures): void
    {
        // Ambil data yang lama dan baru
        $original = $cashExpenditures->getOriginal();
        $changed = $cashExpenditures->getChanges();

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
                'code' => $cashExpenditures->code,
                'action' => 'update',
                'model' => 'CashEx',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(CashExpenditures $cashExpenditures): void
    {
        ActivityLog::create([
            'code' => $cashExpenditures->code,
            'action' => 'delet',
            'model' => 'CashEx',
            'details' => $cashExpenditures->sekretaris->name . ' created Expenditure : ' . $cashExpenditures->anggaran->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(CashExpenditures $cashExpenditures): void
    {
        ActivityLog::create([
            'code' => $cashExpenditures->code,
            'action' => 'restore',
            'model' => 'CashEx',
            'details' => $cashExpenditures->sekretaris->name . ' created Expenditure : ' . $cashExpenditures->anggaran->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(CashExpenditures $cashExpenditures): void
    {
        //
    }
}