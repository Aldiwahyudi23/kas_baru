<?php

namespace App\Livewire\Pengeluaran;

use App\Models\CashExpenditures;
use Livewire\Component;

class TabelPengeluaran extends Component
{
    public $selectedAnggaran = null; // ID anggaran yang dipilih
    public $cashExpenditures = []; // Data CashExpenditures

    protected $listeners = ['updateAnggaran']; // Listener untuk menerima data dari komponen lain

    public function updateAnggaran($anggaranId)
    {
        $this->selectedAnggaran = $anggaranId;

        // Ambil data CashExpenditures berdasarkan anggaran yang dipilih
        $this->cashExpenditures = CashExpenditures::where('anggaran_id', $anggaranId)->get();
    }
    public function render()
    {
        return view('livewire.pengeluaran.tabel-pengeluaran');
    }
}
