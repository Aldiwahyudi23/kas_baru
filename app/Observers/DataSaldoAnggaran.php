<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\AnggaranSaldo;
use App\Models\Saldo;
use Illuminate\Support\Facades\Auth;

class DataSaldoAnggaran
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(AnggaranSaldo $anggaranSaldo): void
    {
        $code = Saldo::FindOrFail($anggaranSaldo->saldo_id);
        ActivityLog::create([
            'code' => $code->code,
            'action' => 'create',
            'model' => 'AnggaranSaldo',
            'details' => 'Saldo : ' . $anggaranSaldo->type . ' => ' . $anggaranSaldo->percentage . '% - ' . $anggaranSaldo->amount . ' - ' . $anggaranSaldo->saldo,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(AnggaranSaldo $anggaranSaldo): void
    {
        // Ambil data yang lama dan baru
        $original = $anggaranSaldo->getOriginal();
        $changed = $anggaranSaldo->getChanges();

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
                'code' => $anggaranSaldo->code,
                'action' => 'update',
                'model' => 'AnggaranSaldo',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(AnggaranSaldo $anggaranSaldo): void
    {
        ActivityLog::create([
            'code' => $anggaranSaldo->code,
            'action' => 'delet',
            'model' => 'AnggaranSaldo',
            'details' => 'Saldo : ' . $anggaranSaldo->type . ' => ' . $anggaranSaldo->percentage . '% - ' . $anggaranSaldo->amount . ' - ' . $anggaranSaldo->saldo,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(AnggaranSaldo $anggaranSaldo): void
    {
        ActivityLog::create([
            'code' => $anggaranSaldo->code,
            'action' => 'restore',
            'model' => 'AnggaranSaldo',
            'details' => 'Saldo ' . $anggaranSaldo->type . ' => ' . $anggaranSaldo->percentage . '% - ' . $anggaranSaldo->amount . ' - ' . $anggaranSaldo->saldo,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(AnggaranSaldo $anggaranSaldo): void
    {
        //
    }
}