<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Anggaran;
use Illuminate\Support\Facades\Auth;

class DataAnggaranAdmin
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(Anggaran $anggaran): void
    {
        ActivityLog::create([
            'code' => $anggaran->code,
            'action' => 'create',
            'model' => 'Anggaran',
            'details' => 'Admin created: ' . $anggaran->name,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(Anggaran $anggaran): void
    {
        // Ambil data yang lama dan baru
        $original = $anggaran->getOriginal();
        $changed = $anggaran->getChanges();

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
                'code' => $anggaran->code,
                'action' => 'update',
                'model' => 'Anggaran',
                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(Anggaran $anggaran): void
    {
        ActivityLog::create([
            'code' => $anggaran->code,
            'action' => 'delet',
            'model' => 'Anggaran',
            'details' => 'Admin deletd: ' . $anggaran->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(Anggaran $anggaran): void
    {
        ActivityLog::create([
            'code' => $anggaran->code,
            'action' => 'restore',
            'model' => 'Anggaran',
            'details' => 'Admin restored: ' . $anggaran->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(Anggaran $anggaran): void
    {
        //
    }
}