<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\DataWarga;
use App\Models\StatusPekerjaan;
use Illuminate\Support\Facades\Auth;

class DataPekerjaanAdmin
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(StatusPekerjaan $statusPekerjaan): void
    {
        $code = DataWarga::FindOrFail($statusPekerjaan->data_warga_id);
        ActivityLog::create([
            'code' => $code->code,
            'action' => 'create',
            'model' => 'StatusPekerjaan',
            'details' => 'Admin created pekerjaan: ' . $statusPekerjaan->status . ' ' . $statusPekerjaan->pekerjaan,
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(StatusPekerjaan $statusPekerjaan): void
    {
        // Ambil data yang lama dan baru
        $original = $statusPekerjaan->getOriginal();
        $changed = $statusPekerjaan->getChanges();

        // Daftar atribut yang ingin dikecualikan
        $excludedAttributes = ['updated_at'];

        foreach ($changed as $key => $value) {
            // Lewati atribut yang dikecualikan
            if (in_array($key, $excludedAttributes)) {
                continue;
            }

            // Buat deskripsi perubahan
            $description = "Updated {$key} from {$original[$key]} to {$value}";

            $code = DataWarga::FindOrFail($statusPekerjaan->data_warga_id);

            ActivityLog::create([
                'code' => $code->code,
                'action' => 'update',
                'model' => 'StatusPekerjaan',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(StatusPekerjaan $statusPekerjaan): void
    {
        $code = DataWarga::FindOrFail($statusPekerjaan->data_warga_id);
        ActivityLog::create([
            'code' => $code->code,
            'action' => 'delet',
            'model' => 'StatusPekerjaan',
            'details' => 'Admin deletd pekerjaan: ' . $statusPekerjaan->status . ' ' . $statusPekerjaan->pekerjaan,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(StatusPekerjaan $statusPekerjaan): void
    {
        $code = DataWarga::FindOrFail($statusPekerjaan->data_warga_id);
        ActivityLog::create([
            'code' => $code->code,
            'action' => 'restore',
            'model' => 'StatusPekerjaan',
            'details' => 'Admin restored pekerjaan: ' . $statusPekerjaan->status . ' ' . $statusPekerjaan->pekerjaan,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(StatusPekerjaan $statusPekerjaan): void
    {
        //
    }
}