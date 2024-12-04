<?php

namespace App\Livewire;

use App\Models\Saldo;
use Livewire\Component;

class SaldoDashboardUser extends Component
{

    public $saldo;

    protected $listeners = ['saldoUpdated' => 'updateSaldo'];

    public function mount()
    {
        $this->updateSaldo();
    }

    public function updateSaldo()
    {
        $this->saldo = Saldo::latest('updated_at')->value('total_balance') ?? 0;
    }
    public function render()
    {
        return view('livewire.saldo-dashboard-user');
    }
}
