<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\DataWarga;
use Illuminate\Support\Facades\Auth;

class DataWargaAdmin
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(DataWarga $dataWarga): void
    {
        ActivityLog::create([
            'code' => $dataWarga->code,
            'action' => 'create',
            'model' => 'DataWarga',
            'details' => 'Admin created: ' . $dataWarga->name,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(DataWarga $dataWarga): void
    {
        // Ambil data yang lama dan baru
        $original = $dataWarga->getOriginal();
        $changed = $dataWarga->getChanges();

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
                'code' => $dataWarga->code,
                'action' => 'update',
                'model' => 'DataWarga',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(DataWarga $dataWarga): void
    {
        ActivityLog::create([
            'code' => $dataWarga->code,
            'action' => 'delet',
            'model' => 'DataWarga',
            'details' => 'Admin deletd: ' . $dataWarga->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(DataWarga $dataWarga): void
    {
        ActivityLog::create([
            'code' => $dataWarga->code,
            'action' => 'restore',
            'model' => 'DataWarga',
            'details' => 'Admin restored: ' . $dataWarga->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(DataWarga $dataWarga): void
    {
        //
    }
}