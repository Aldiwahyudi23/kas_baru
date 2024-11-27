<?php

namespace App\Livewire\Pengeluaran;

use App\Models\Anggaran;
use App\Models\CashExpenditures;
use Livewire\Component;

class PengeluaranAnggaran extends Component
{
    public $anggaran = []; // Data anggaran
    public $selectedAnggaran = null; // ID anggaran yang dipilih
    public $anggaranStatus = []; // Menyimpan status terkait anggaran

    public function mount()
    {
        // Ambil semua data anggaran
        $this->anggaran = Anggaran::all();

        // Ambil status CashExpenditures untuk setiap anggaran
        foreach ($this->anggaran as $item) {
            $cashExpenditure = CashExpenditures::where('anggaran_id', $item->id)
                ->where('status', '!=', 'Acknowledged')
                ->first();

            $this->anggaranStatus[$item->id] = $cashExpenditure
                ? $cashExpenditure->status // Status jika ada selain 'Acknowledged'
                : null; // Tidak ada CashExpenditures atau semua 'Acknowledged'
        }
    }

    public function updatedSelectedAnggaran($value)
    {
        // Emit event ke komponen lain
        $this->emit('updateAnggaran', $value);
    }
    public function render()
    {
        return view('livewire.pengeluaran.pengeluaran-anggaran');
    }
}
