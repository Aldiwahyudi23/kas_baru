<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Deposit;

class DataDeposit
{
    /**
     * Handle the Deposit "created" event.
     */
    public function created(Deposit $deposit): void
    {
        ActivityLog::create([
            'code' => $deposit->code,
            'action' => 'create',
            'model' => 'deposit',
            'details' => $deposit->submit->name . ' created deposite: ' . $deposit->amount,
        ]);
    }

    /**
     * Handle the Deposit "updated" event.
     */
    public function updated(Deposit $deposit): void
    {
        // Ambil data yang lama dan baru
        $original = $deposit->getOriginal();
        $changed = $deposit->getChanges();

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
                'code' => $deposit->code,
                'action' => 'update',
                'model' => 'deposit',
                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Deposit "deleted" event.
     */
    public function deleted(Deposit $deposit): void
    {
        ActivityLog::create([
            'code' => $deposit->code,
            'action' => 'delete',
            'model' => 'deposit',
            'details' => $deposit->submit->name . ' created deposite: ' . $deposit->amount,
        ]);
    }

    /**
     * Handle the Deposit "restored" event.
     */
    public function restored(Deposit $deposit): void
    {
        ActivityLog::create([
            'code' => $deposit->code,
            'action' => 'restore',
            'model' => 'deposit',
            'details' => $deposit->submit->name . ' created deposite: ' . $deposit->amount,
        ]);
    }

    /**
     * Handle the Deposit "force deleted" event.
     */
    public function forceDeleted(Deposit $deposit): void
    {
        //
    }
}
