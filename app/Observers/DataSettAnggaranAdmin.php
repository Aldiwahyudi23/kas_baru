<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\AnggaranSetting;
use Illuminate\Support\Facades\Auth;

class DataSettAnggaranAdmin
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(AnggaranSetting $anggaranSetting): void
    {
        ActivityLog::create([
            'code' => $anggaranSetting->anggaran->code,
            'action' => 'create',
            'model' => 'AnggaranSetting',
            'details' => 'Admin created ' . $anggaranSetting->label_anggaran . ' => ' . $anggaranSetting->catatan_anggaran,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(AnggaranSetting $anggaranSetting): void
    {
        // Ambil data yang lama dan baru
        $original = $anggaranSetting->getOriginal();
        $changed = $anggaranSetting->getChanges();

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
                'code' => $anggaranSetting->anggaran->code,
                'action' => 'update',
                'model' => 'AnggaranSetting',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(AnggaranSetting $anggaranSetting): void
    {

        ActivityLog::create([
            'code' => $anggaranSetting->anggaran->code,
            'action' => 'delete',
            'model' => 'AnggaranSetting',
            'details' => 'Admin created ' . $anggaranSetting->label_anggaran . ' => ' . $anggaranSetting->catatan_anggaran,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(anggaranSetting $anggaranSetting): void
    {
        //
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(anggaranSetting $anggaranSetting): void
    {
        //
    }
}