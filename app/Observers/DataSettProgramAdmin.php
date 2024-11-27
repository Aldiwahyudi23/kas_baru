<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Program;
use App\Models\ProgramSetting;
use Illuminate\Support\Facades\Auth;

class DataSettProgramAdmin
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(ProgramSetting $programSetting): void
    {
        ActivityLog::create([
            'code' => $programSetting->program->code,
            'action' => 'create',
            'model' => 'ProgramSetting',
            'details' => 'Admin created ' . $programSetting->label_program . ' => ' . $programSetting->catatan_program,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(ProgramSetting $programSetting): void
    {
        // Ambil data yang lama dan baru
        $original = $programSetting->getOriginal();
        $changed = $programSetting->getChanges();

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
                'code' => $programSetting->program->code,
                'action' => 'update',
                'model' => 'ProgramSetting',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(ProgramSetting $programSetting): void
    {

        ActivityLog::create([
            'code' => $programSetting->program->code,
            'action' => 'delete',
            'model' => 'ProgramSetting',
            'details' => 'Admin created ' . $programSetting->label_program . ' => ' . $programSetting->catatan_program,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(ProgramSetting $programSetting): void
    {
        //
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(ProgramSetting $programSetting): void
    {
        //
    }
}