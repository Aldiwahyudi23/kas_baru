<?php

namespace App\Livewire\Konter;

use App\Models\Konter\KategoriKonter;
use App\Models\Konter\ProviderKonter;
use Livewire\Component;

class ProviderKonterCrud extends Component
{
    public $kategori_id, $name, $description, $providerId, $successMessage; // Properti untuk form
    public $isEditing = false; // Status editing
    public $isModalOpen = false;

    protected $rules = [
        'kategori_id' => 'required|exists:kategori_konters,id',
        'name' => 'required|unique:provider_konters,name',
        'description' => 'required',
    ];

    protected $messages = [
        'kategori_id.required' => 'Kategori wajib dipilih.',
        'kategori_id.exists' => 'Kategori tidak valid.',
        'name.required' => 'Nama wajib diisi.',
        'name.unique' => 'Nama sudah digunakan.',
        'description.required' => 'Deskripsi wajib diisi.',
    ];

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->resetInput();
        $this->isModalOpen = false;
    }

    public function render()
    {
        $kategoris = KategoriKonter::all(); // Ambil semua kategori
        $providers = ProviderKonter::with('kategori')->get(); // Ambil semua provider dengan kategori
        return view('livewire.konter.provider-konter-crud', compact('kategoris', 'providers'));
    }

    public function resetInput()
    {
        $this->kategori_id = null;
        $this->name = '';
        $this->description = '';
        $this->providerId = null;
        $this->isEditing = false;
    }

    public function store()
    {
        $this->validate();

        ProviderKonter::create([
            'kategori_id' => $this->kategori_id,
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->resetInput();
        $this->closeModal();
        // Set success message
        $this->successMessage = 'Data berhasil disimpan.';
        
    }

    public function edit($id)
    {
        $provider = ProviderKonter::find($id);
        $this->providerId = $provider->id;
        $this->kategori_id = $provider->kategori_id;
        $this->name = $provider->name;
        $this->description = $provider->description;
        $this->isEditing = true;
        $this->openModal();
    }

    public function update()
    {
        $this->validate([
            'kategori_id' => 'required|exists:kategori_konters,id',
            'name' => 'required|unique:provider_konters,name,' . $this->providerId,
            'description' => 'required',
        ]);

        $provider = ProviderKonter::find($this->providerId);
        $provider->update([
            'kategori_id' => $this->kategori_id,
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->resetInput();
        $this->closeModal();
        $this->successMessage = 'Data berhasil diperbaharui.';
    }

    public function delete($id)
    {
        ProviderKonter::destroy($id);
        session()->flash('message', 'Provider deleted successfully.');
    }
}
