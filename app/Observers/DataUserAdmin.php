<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\DataWarga;
use App\Models\user;
use Illuminate\Support\Facades\Auth;

class DataUserAdmin
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(user $user): void
    {
        $code = DataWarga::FindOrFail($user->data_warga_id);
        ActivityLog::create([
            'code' => $code->code,
            'action' => 'create',
            'model' => 'User',
            'details' => 'Admin created USER: ' . $user->name . ' ' . $user->role->name,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(user $user): void
    {
        // Ambil data yang lama dan baru
        $original = $user->getOriginal();
        $changed = $user->getChanges();

        // Daftar atribut yang ingin dikecualikan
        $excludedAttributes = ['updated_at'];

        foreach ($changed as $key => $value) {
            // Lewati atribut yang dikecualikan
            if (in_array($key, $excludedAttributes)) {
                continue;
            }

            // Buat deskripsi perubahan
            $description = "Updated {$key} from {$original[$key]} to {$value}";

            $code = DataWarga::FindOrFail($user->data_warga_id);

            ActivityLog::create([
                'code' => $code->code,
                'action' => 'update',
                'model' => 'User',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(user $user): void
    {
        $code = DataWarga::FindOrFail($user->data_warga_id);
        ActivityLog::create([
            'code' => $code->code,
            'action' => 'delet',
            'model' => 'User',
            'details' =>
            'Admin deletd USER: ' . $user->name . ' ' . $user->role->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(user $user): void
    {
        $code = DataWarga::FindOrFail($user->data_warga_id);
        ActivityLog::create([
            'code' => $code->code,
            'action' => 'restore',
            'model' => 'User',
            'details' =>
            'Admin restored USER: ' . $user->name . ' ' . $user->role->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(user $user): void
    {
        //
    }
}