<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\AllRouteUrl;
use Illuminate\Support\Facades\Auth;

class DataRouteUrlAdmin
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(AllRouteUrl $allRouteUrl): void
    {
        ActivityLog::create([
            'code' => $allRouteUrl->code,
            'action' => 'create',
            'model' => 'RouteUrl',
            'details' => 'Admin created: ' . $allRouteUrl->name,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(AllRouteUrl $allRouteUrl): void
    {
        // Ambil data yang lama dan baru
        $original = $allRouteUrl->getOriginal();
        $changed = $allRouteUrl->getChanges();

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
                'code' => $allRouteUrl->code,
                'action' => 'update',
                'model' => 'RouteUrl',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(AllRouteUrl $allRouteUrl): void
    {
        ActivityLog::create([
            'code' => $allRouteUrl->code,
            'action' => 'delet',
            'model' => 'RouteUrl',
            'details' => 'Admin deletd: ' . $allRouteUrl->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(AllRouteUrl $allRouteUrl): void
    {
        ActivityLog::create([
            'code' => $allRouteUrl->code,
            'action' => 'restore',
            'model' => 'RouteUrl',
            'details' => 'Admin restored: ' . $allRouteUrl->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(AllRouteUrl $allRouteUrl): void
    {
        //
    }
}