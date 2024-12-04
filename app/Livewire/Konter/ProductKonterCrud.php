<?php

namespace App\Livewire\Konter;

use App\Models\Konter\KategoriKonter;
use App\Models\Konter\ProductKonter;
use App\Models\Konter\ProviderKonter;
use Livewire\Component;

class ProductKonterCrud extends Component
{
    public $kategori_id, 
    $provider_id, 
    $amount, 
    $buying_price, 
    $price, 
    $price1, 
    $price2, 
    $price3, 
    $price4, 
    $productId;

    public $isEditing = false;
    public $isModalOpen = false;
    public $successMessage;

    protected $rules = [
        'kategori_id' => 'required|exists:kategori_konters,id',
        'provider_id' => 'required|exists:provider_konters,id',
        'amount' => 'required|numeric|min:0',
        'buying_price' => 'required|numeric|min:0',
        'price' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'kategori_id.required' => 'Kategori wajib dipilih.',
        'kategori_id.exists' => 'Kategori tidak valid.',
        'provider_id.required' => 'Provider wajib dipilih.',
        'provider_id.exists' => 'Proveider tidak valid.',
        'amount.required' => 'Nominal wajib diisi.',
        'buying_price.required' => 'Harga beli wajib diisi.',
        'price.required' => 'Harga Jual wajib diisi.',
    ];

    public function openModal()
    {
        $this->isModalOpen = true;
    }
    public function closeModal()
    {
        $this->resetForm();
        $this->isModalOpen = false;
    }

    public function store()
    {
        $this->validate();

        // Cek kombinasi unik (kategori_id, provider_id, amount)
        $existingProduct = ProductKonter::where('kategori_id', $this->kategori_id)
        ->where('provider_id', $this->provider_id)
        ->where('amount', $this->amount) // Hilangkan koma pada amount
        ->exists();

        if ($existingProduct) {
            // Jika ada kombinasi yang sama, beri pesan error
            $this->successMessage = 'Produk dengan kombinasi kategori, provider, dan amount ini sudah ada!';
            return;
        }

        ProductKonter::create([
            'kategori_id' => $this->kategori_id,
            'provider_id' => $this->provider_id,
            'amount' => $this->amount,
            'buying_price' => $this->buying_price,
            'price' => $this->price,
            'price1' => $this->price1,
            'price2' => $this->price2,
            'price3' => $this->price3,
            'price4' => $this->price4,
        ]);

        $this->resetForm();
        $this->closeModal();
        $this->successMessage = 'Produk berhasil ditambahkan.';
    }

    public function edit($id)
    {
        $product = ProductKonter::findOrFail($id);
        $this->productId = $product->id;
        $this->kategori_id = $product->kategori_id;
        $this->provider_id = $product->provider_id;
        $this->amount = $product->amount;
        $this->buying_price = $product->buying_price;
        $this->price = $product->price;
        $this->price1 = $product->price1;
        $this->price2 = $product->price2;
        $this->price3 = $product->price3;
        $this->price4 = $product->price4;

        $this->openModal();
    }

    public function update()
    {
        $this->validate();

        $product = ProductKonter::findOrFail($this->productId);
        $product->update([
            'kategori_id' => $this->kategori_id,
            'provider_id' => $this->provider_id,
            'amount' => $this->amount,
            'buying_price' => $this->buying_price,
            'price' => $this->price,
            'price1' => $this->price1,
            'price2' => $this->price2,
            'price3' => $this->price3,
            'price4' => $this->price4,
        ]);

        $this->resetForm();
        $this->closeModal();
        $this->successMessage = 'Produk berhasil diperbarui.';
    }

    public function delete($id)
    {
        ProductKonter::destroy($id);
        $this->successMessage = 'Produk berhasil dihapus.';
    }

    public function resetForm()
    {
        $this->kategori_id = '';
        $this->provider_id = '';
        $this->amount = '';
        $this->buying_price = '';
        $this->price = '';
        $this->productId = null;
    }

    public function render()
    {
        return view('livewire.konter.product-konter-crud', [
            'categories' => KategoriKonter::all(),
            'providers' => ProviderKonter::all(),
            'products' => ProductKonter::with('kategori', 'provider')->get(), // Pastikan relasi di-load di sini
        ]);
    }
}
