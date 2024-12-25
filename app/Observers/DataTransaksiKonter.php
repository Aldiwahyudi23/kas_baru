<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Konter\TransaksiKonter;

class DataTransaksiKonter
{
    /**
     * Handle the TransaksiKonter "created" event.
     */
    public function created(TransaksiKonter $transaksiKonter): void
    {
        ActivityLog::create([
            'code' => $transaksiKonter->code,
            'action' => 'create',
            'model' => 'transaksiKonter',
            'details' => $transaksiKonter->submitted_by . ' created Transaksi: ' . $transaksiKonter->product->kategori->name . " " . $transaksiKonter->product->provider->name . " " . $transaksiKonter->product->amount,
        ]);
    }

    /**
     * Handle the TransaksiKonter "updated" event.
     */
    public function updated(TransaksiKonter $transaksiKonter): void
    {
        // Ambil data yang lama dan baru
        $original = $transaksiKonter->getOriginal();
        $changed = $transaksiKonter->getChanges();

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
                'code' => $transaksiKonter->code,
                'action' => 'update',
                'model' => 'transaksiKonter',
                'details' => $description,
            ]);
        }
    }

    /**
     * Handle the TransaksiKonter "deleted" event.
     */
    public function deleted(TransaksiKonter $transaksiKonter): void
    {
        ActivityLog::create([
            'code' => $transaksiKonter->code,
            'action' => 'Delete',
            'model' => 'transaksiKonter',
            'details' => $transaksiKonter->submitted_by . ' Delete Transaksi: ' . $transaksiKonter->product->kategori->name . " " . $transaksiKonter->product->provider->name . " " . $transaksiKonter->product->amount,
        ]);
    }

    /**
     * Handle the TransaksiKonter "restored" event.
     */
    public function restored(TransaksiKonter $transaksiKonter): void
    {
        //
    }

    /**
     * Handle the TransaksiKonter "force deleted" event.
     */
    public function forceDeleted(TransaksiKonter $transaksiKonter): void
    {
        //
    }
}
