<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class DataAdminObserver
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(Admin $admin): void
    {
        ActivityLog::create([
            'code' => $admin->code,
            'action' => 'create',
            'model' => 'Admin',
            'details' => 'Admin created: ' . $admin->name,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(Admin $admin): void
    {
        // Ambil data yang lama dan baru
        $original = $admin->getOriginal();
        $changed = $admin->getChanges();

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
                'code' => $admin->code,
                'action' => 'update',
                'model' => 'Admin',
                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(Admin $admin): void
    {
        ActivityLog::create([
            'code' => $admin->code,
            'action' => 'delet',
            'model' => 'Admin',
            'details' => 'Admin deletd: ' . $admin->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(Admin $admin): void
    {
        ActivityLog::create([
            'code' => $admin->code,
            'action' => 'restore',
            'model' => 'Admin',
            'details' => 'Admin restored: ' . $admin->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(Admin $admin): void
    {
        //
    }
}