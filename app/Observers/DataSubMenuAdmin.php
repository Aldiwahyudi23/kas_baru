<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\SubMenu;
use Illuminate\Support\Facades\Auth;

class DataSubMenuAdmin

{
    /**
     * Handle the Admin "created" event.
     */
    public function created(SubMenu $subMenu): void
    {
        ActivityLog::create([
            'code' => $subMenu->code,
            'action' => 'create',
            'model' => 'SubMenu',
            'details' => 'Admin created: ' . $subMenu->name,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(SubMenu $subMenu): void
    {
        // Ambil data yang lama dan baru
        $original = $subMenu->getOriginal();
        $changed = $subMenu->getChanges();

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
                'code' => $subMenu->code,
                'action' => 'update',
                'model' => 'SubMenu',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(SubMenu $subMenu): void
    {
        ActivityLog::create([
            'code' => $subMenu->code,
            'action' => 'delet',
            'model' => 'SubMenu',
            'details' => 'Admin deletd: ' . $subMenu->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(SubMenu $subMenu): void
    {
        ActivityLog::create([
            'code' => $subMenu->code,
            'action' => 'restore',
            'model' => 'SubMenu',
            'details' => 'Admin restored: ' . $subMenu->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(SubMenu $subMenu): void
    {
        //
    }
}