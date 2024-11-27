<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Program;
use Illuminate\Support\Facades\Auth;

class DataProgramAdmin

{
    /**
     * Handle the Admin "created" event.
     */
    public function created(Program $program): void
    {
        ActivityLog::create([
            'code' => $program->code,
            'action' => 'create',
            'model' => 'Program',
            'details' => 'Admin created: ' . $program->name,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(Program $program): void
    {
        // Ambil data yang lama dan baru
        $original = $program->getOriginal();
        $changed = $program->getChanges();

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
                'code' => $program->code,
                'action' => 'update',
                'model' => 'Program',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(Program $program): void
    {
        ActivityLog::create([
            'code' => $program->code,
            'action' => 'delet',
            'model' => 'Program',
            'details' => 'Admin deletd: ' . $program->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(Program $program): void
    {
        ActivityLog::create([
            'code' => $program->code,
            'action' => 'restore',
            'model' => 'Program',
            'details' => 'Admin restored: ' . $program->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(Program $program): void
    {
        //
    }
}