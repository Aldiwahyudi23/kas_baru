<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class DataMenuAdmin

{
    /**
     * Handle the Admin "created" event.
     */
    public function created(Menu $menu): void
    {
        ActivityLog::create([
            'code' => $menu->code,
            'action' => 'create',
            'model' => 'Menu',
            'details' => 'Admin created: ' . $menu->name,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(Menu $menu): void
    {
        // Ambil data yang lama dan baru
        $original = $menu->getOriginal();
        $changed = $menu->getChanges();

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
                'code' => $menu->code,
                'action' => 'update',
                'model' => 'Menu',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(Menu $menu): void
    {
        ActivityLog::create([
            'code' => $menu->code,
            'action' => 'delet',
            'model' => 'Menu',
            'details' => 'Admin deletd: ' . $menu->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(Menu $menu): void
    {
        ActivityLog::create([
            'code' => $menu->code,
            'action' => 'restore',
            'model' => 'Menu',
            'details' => 'Admin restored: ' . $menu->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(Menu $menu): void
    {
        //
    }
}