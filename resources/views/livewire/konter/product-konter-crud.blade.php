<div>
    <div class="mb-4">
        <button wire:click="openModal" class="btn btn-primary">Tambah Prodcut</button>
    </div>

    <!-- Success Message -->
    @if ($successMessage)
    <div class="alert alert-success">
        {{ $successMessage }}
    </div>
    @endif

    @if ($isModalOpen)
    <!-- Form -->
    <form wire:submit.prevent="{{ $productId ? 'update' : 'store' }}">
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select wire:model="kategori_id" id="kategori_id" class="form-control @error('kategori_id') is-invalid @enderror">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="provider_id">Provider</label>
                    <select wire:model="provider_id" id="provider_id" class="form-control @error('provider_id') is-invalid @enderror">
                        <option value="">Pilih Provider</option>
                        @foreach ($providers as $provider)
                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                        @endforeach
                    </select>
                    @error('provider_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="amount">Jumlah</label>
                    <input type="number" wire:model="amount" id="amount" class="form-control @error('amount') is-invalid @enderror">
                    @error('amount') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="buying_price">Harga Beli</label>
                    <input type="number" wire:model="buying_price" id="buying_price" class="form-control @error('buying_price') is-invalid @enderror">
                    @error('buying_price') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="price">Harga Jual</label>
                    <input type="number" wire:model="price" id="price" class="form-control @error('price') is-invalid @enderror">
                    @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="form-group">
                    <label for="price1">Harga Jual 1 Minggu</label>
                    <input type="number" wire:model="price1" id="price" class="form-control @error('price1') is-invalid @enderror">
                    @error('price1') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="price2">Harga Jual 2 Minggu</label>
                    <input type="number" wire:model="price2" id="price" class="form-control @error('price2') is-invalid @enderror">
                    @error('price2') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="price3">Harga Jual 3 Minggu</label>
                    <input type="number" wire:model="price3" id="price" class="form-control @error('price3') is-invalid @enderror">
                    @error('price3') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="price4">Harga Jual 4 Minggu</label>
                    <input type="number" wire:model="price4" id="price" class="form-control @error('price4') is-invalid @enderror">
                    @error('price4') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <button type="button" wire:click="closeModal" class="btn btn-warning" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">{{ $productId ? 'Update' : 'Simpan' }}</button>
    </form>
    @endif

    <!-- Tabel Data -->
    <table class="table mt-4 datatable">
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Provider</th>
                <th>Jumlah</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Harga Jual 1</th>
                <th>Harga Jual 2</th>
                <th>Harga Jual 3</th>
                <th>Harga Jual 4</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $product->kategori->name ?? '-' }}</td>
                <td>{{ $product->provider->name ?? '-' }}</td>
                <td>{{ number_format($product->amount, 0, ',', '.') }}</td>
                <td>{{ number_format($product->buying_price, 0, ',', '.') }}</td>
                <td>{{ number_format($product->price, 0, ',', '.') }}</td>
                <td>{{ number_format($product->price1, 0, ',', '.') }}</td>
                <td>{{ number_format($product->price2, 0, ',', '.') }}</td>
                <td>{{ number_format($product->price3, 0, ',', '.') }}</td>
                <td>{{ number_format($product->price4, 0, ',', '.') }}</td>
                <td>
                    <button wire:click="edit({{ $product->id }})" class="btn btn-warning btn-sm">Edit</button>
                    <button wire:click="delete({{ $product->id }})" class="btn btn-danger btn-sm">Hapus</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>