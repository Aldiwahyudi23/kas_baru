<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class DataRoleAdmin
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(Role $role): void
    {
        ActivityLog::create([
            'code' => $role->code,
            'action' => 'create',
            'model' => 'Role',
            'details' => 'Admin created: ' . $role->name,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(Role $role): void
    {
        // Ambil data yang lama dan baru
        $original = $role->getOriginal();
        $changed = $role->getChanges();

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
                'code' => $role->code,
                'action' => 'update',
                'model' => 'Role',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(Role $role): void
    {
        ActivityLog::create([
            'code' => $role->code,
            'action' => 'delet',
            'model' => 'Role',
            'details' => 'Admin deletd: ' . $role->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(Role $role): void
    {
        ActivityLog::create([
            'code' => $role->code,
            'action' => 'restore',
            'model' => 'Role',
            'details' => 'Admin restored: ' . $role->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(role $role): void
    {
        //
    }
}