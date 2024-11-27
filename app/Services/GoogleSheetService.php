<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSheetService
{
    protected $spreadsheetId;
    protected $service;

    public function __construct()
    {
        $this->spreadsheetId = config('1iRixzWKvntsIXHJwa-M3pomi_A7l30KVuAgtwgVYTgI'); // Pastikan ID spreadsheet ada di konfigurasi

        $client = new Client();
        $client->setApplicationName('Keluarga_ma_HAYA');
        $client->setScopes([Sheets::SPREADSHEETS]);
        $client->setAuthConfig(storage_path('app/google/kas-keluarga-777d8763b783.json')); // Letakkan credential JSON Anda di path ini
        $client->setAccessType('offline');

        $this->service = new Sheets($client);
    }

    /**
     * Tambahkan baris baru di Google Sheet.
     *
     * @param array $data
     * @param string $range
     * @return void
     */
    public function appendRow(array $data, $range = 'kas!2')
    {
        $body = new ValueRange(['values' => [$data]]);
        $params = ['valueInputOption' => 'RAW'];

        $this->service->spreadsheets_values->append($this->spreadsheetId, $range, $body, $params);
    }

    /**
     * Temukan baris berdasarkan ID di kolom tertentu.
     *
     * @param string $id
     * @param string $range
     * @return int|null
     */
    public function findRowById($id, $range = 'kas!A:A')
    {
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();

        if ($values) {
            foreach ($values as $index => $row) {
                if (isset($row[0]) && $row[0] == $id) {
                    return $index + 1; // Nomor baris di Google Sheets
                }
            }
        }
        return null;
    }

    /**
     * Perbarui data pada baris tertentu di Google Sheet.
     *
     * @param int $rowNumber
     * @param array $data
     * @param string $range
     * @return void
     */
    public function updateRow($rowNumber, array $data, $range = 'kas!')
    {
        $range = $range . 'A' . $rowNumber;
        $body = new ValueRange(['values' => [$data]]);
        $params = ['valueInputOption' => 'RAW'];

        $this->service->spreadsheets_values->update($this->spreadsheetId, $range, $body, $params);
    }
}
