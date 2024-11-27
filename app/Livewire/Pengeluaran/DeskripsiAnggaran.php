<?php

namespace App\Livewire\Pengeluaran;

use Livewire\Component;
use App\Models\Anggaran;

class DeskripsiAnggaran extends Component
{
    public $selectedAnggaran = null; // ID anggaran yang dipilih
    public $description = ''; // Deskripsi anggaran

    protected $listeners = ['updateAnggaran']; // Listener untuk menerima data dari komponen lain

    public function updateAnggaran($anggaranId)
    {
        $this->selectedAnggaran = $anggaranId;

        // Ambil deskripsi berdasarkan anggaran yang dipilih
        $anggaran = Anggaran::find($anggaranId);
        $this->description = $anggaran ? $anggaran->description : 'Deskripsi tidak ditemukan.';
    }
    public function render()
    {
        return view('livewire.pengeluaran.deskripsi-anggaran');
    }
}
