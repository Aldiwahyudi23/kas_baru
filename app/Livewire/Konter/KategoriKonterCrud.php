<?php

namespace App\Livewire\Konter;

use App\Models\Konter\KategoriKonter;
use Illuminate\Validation\Rule;
use Livewire\Component;

class KategoriKonterCrud extends Component
{
    public $kategoriKonters, $name, $description, $kategoriId;
    public $isEditing = false; // Status editing
    public $isModalOpen = false;

    protected $rules = [
        'name' => 'required|unique:kategori_konters,name',
        'description' => 'required',
    ];

    protected $messages = [
        'name.required' => 'Nama wajib diisi.',
        'name.unique' => 'Nama sudah digunakan.',
        'description.required' => 'Deskripsi wajib diisi.',
    ];

    public function render()
    {
        $this->kategoriKonters = KategoriKonter::all();
        return view('livewire.konter.kategori-konter-crud');
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->resetInput();
        $this->isModalOpen = false;
    }

    public function resetInput()
    {
        $this->name = '';
        $this->description = '';
        $this->kategoriId = null;
        $this->isEditing = false;
    }

    public function store()
    {
        $this->validate();

        KategoriKonter::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->resetInput();
        $this->closeModal();
        session()->flash('message', 'Kategori Created successfully.');
    }

    public function edit($id)
    {
        $kategori = KategoriKonter::find($id);
        $this->kategoriId = $kategori->id;
        $this->name = $kategori->name;
        $this->description = $kategori->description;
        $this->isEditing = true;
        $this->openModal();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|unique:kategori_konters,name,' . $this->kategoriId,
            'description' => 'required',
        ]);

        $kategori = KategoriKonter::find($this->kategoriId);
        $kategori->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->resetInput();
        $this->closeModal();
        session()->flash('message', 'Kategori updated successfully.');
    }

    public function delete($id)
    {
        KategoriKonter::destroy($id);

        session()->flash('message', 'Kategori deleted successfully.');
    }
}
