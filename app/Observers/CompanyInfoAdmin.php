<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\CompanyInformation;
use Illuminate\Support\Facades\Auth;

class CompanyInfoAdmin
{
    /**
     * Handle the CompanyInformation "created" event.
     */
    public function created(CompanyInformation $companyInformation): void
    {
        ActivityLog::create([
            'code' => $companyInformation->code,
            'action' => 'create',
            'model' => 'CompanyInformation',
            'details' => 'Admin created: ' . $companyInformation->name,
        ]);
    }

    /**
     * Handle the CompanyInformation "updated" event.
     */
    public function updated(CompanyInformation $companyInformation): void
    {
        // Ambil data yang lama dan baru
        $original = $companyInformation->getOriginal();
        $changed = $companyInformation->getChanges();

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
                'code' => $companyInformation->code,
                'action' => 'update',
                'model' => 'Admin',

                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the CompanyInformation "deleted" event.
     */
    public function deleted(CompanyInformation $companyInformation): void
    {
        ActivityLog::create([
            'code' => $companyInformation->code,
            'action' => 'delete',
            'model' => 'CompanyInformation',
            'details' => 'Admin deleted: ' . $companyInformation->name,
        ]);
    }

    /**
     * Handle the CompanyInformation "restored" event.
     */
    public function restored(CompanyInformation $companyInformation): void
    {
        ActivityLog::create([
            'code' => $companyInformation->code,
            'action' => 'restore',
            'model' => 'CompanyInformation',
            'details' => 'Admin restored: ' . $companyInformation->name,
        ]);
    }

    /**
     * Handle the CompanyInformation "force deleted" event.
     */
    public function forceDeleted(CompanyInformation $companyInformation): void
    {
        //
    }
}