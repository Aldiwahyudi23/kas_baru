<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Saldo;
use Illuminate\Support\Facades\Auth;

class DataSaldoAdmin
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(Saldo $saldo): void
    {
        ActivityLog::create([
            'code' => $saldo->code,
            'action' => 'create',
            'model' => 'Saldo',
            'details' => 'Saldo : ' . $saldo->ending_balance . ' - ' . $saldo->amount . ' - ' . $saldo->total_balance,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(Saldo $saldo): void
    {
        // Ambil data yang lama dan baru
        $original = $saldo->getOriginal();
        $changed = $saldo->getChanges();

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
                'code' => $saldo->code,
                'action' => 'update',
                'model' => 'Saldo',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(Saldo $saldo): void
    {
        ActivityLog::create([
            'code' => $saldo->code,
            'action' => 'delet',
            'model' => 'Saldo',
            'details' => 'Saldo : ' . $saldo->ending_balance . ' - ' . $saldo->amount . ' - ' . $saldo->total_balance,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(Saldo $saldo): void
    {
        ActivityLog::create([
            'code' => $saldo->code,
            'action' => 'restore',
            'model' => 'Saldo',
            'details' => 'Saldo : ' . $saldo->ending_balance . ' - ' . $saldo->amount . ' - ' . $saldo->total_balance,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(Saldo $saldo): void
    {
        //
    }
}