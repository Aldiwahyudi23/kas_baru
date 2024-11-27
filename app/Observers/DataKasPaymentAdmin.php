<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\KasPayment;
use App\Services\GoogleSheetService;
use Illuminate\Support\Facades\Auth;

class DataKasPaymentAdmin
{

    // protected $googleSheetService;

    // public function __construct(GoogleSheetService $googleSheetService)
    // {
    //     $this->googleSheetService = $googleSheetService;
    // }
    /**
     * Handle the Admin "created" event.
     */
    public function created(KasPayment $kasPayment): void
    {
        ActivityLog::create([
            'code' => $kasPayment->code,
            'action' => 'create',
            'model' => 'KasPay',
            'details' => $kasPayment->submitted->name . ' created: ' . $kasPayment->data_warga->name,
        ]);

        // Kirim data ke Google Sheets setelah loan berhasil disimpan
        // $data = [
        //     $kasPayment->id,
        //     $kasPayment->submitted->name,
        //     $kasPayment->data_warga->name,
        //     $kasPayment->amount,
        //     $kasPayment->status,
        //     $kasPayment->created_at,
        // ];

        // $this->googleSheetService->appendRow($data);
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(KasPayment $kasPayment): void
    {
        // Ambil data yang lama dan baru
        $original = $kasPayment->getOriginal();
        $changed = $kasPayment->getChanges();

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
                'code' => $kasPayment->code,
                'action' => 'update',
                'model' => 'KasPay',

                'details' => $description,
            ]);
        }

        // $googleSheetService = app(GoogleSheetService::class);
        // $rowNumber = $googleSheetService->findRowById($kasPayment->id,);

        // if ($rowNumber) {
        //     $data = [
        //         $kasPayment->id,
        //         $kasPayment->submitted->name,
        //         $kasPayment->data_warga->name,
        //         $kasPayment->amount,
        //         $kasPayment->status,
        //         $kasPayment->created_at,
        //     ];
        //     $googleSheetService->updateRow($rowNumber, $data);
        // }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(KasPayment $kasPayment): void
    {
        ActivityLog::create([
            'code' => $kasPayment->code,
            'action' => 'delet',
            'model' => 'KasPay',
            'details' => $kasPayment->submitted->name . ' created: ' . $kasPayment->data_warga->name,
        ]);
    }

    /**
     * Handle the Admin "restored" event.
     */
    public function restored(KasPayment $kasPayment): void
    {
        ActivityLog::create([
            'code' => $kasPayment->code,
            'action' => 'restore',
            'model' => 'KasPay',
            'details' => $kasPayment->submitted->name . ' created: ' . $kasPayment->data_warga->name,
        ]);
    }

    /**
     * Handle the Admin "force deleted" event.
     */
    public function forceDeleted(KasPayment $kasPayment): void
    {
        //
    }
}