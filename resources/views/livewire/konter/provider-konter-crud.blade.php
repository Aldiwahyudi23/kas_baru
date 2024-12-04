<div>
    <div class="mb-4">
        <button wire:click="openModal" class="btn btn-primary">Tambah Provider</button>
    </div>
    <!-- Success Message -->
    @if ($successMessage)
    <div class="alert alert-success">
        {{ $successMessage }}
    </div>
    @endif
    
    @if ($isModalOpen)
    <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}">
        <div class="form-group">
            <label>Kategori</label>
            <select wire:model="kategori_id" class="form-control @error('kategori_id') is-invalid @enderror">
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoris as $kategori)
                <option value="{{ $kategori->id }}">{{ $kategori->name }}</option>
                @endforeach
            </select>
            @error('kategori_id') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label>Nama</label>
            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea wire:model="description"
                class="form-control @error('description') is-invalid @enderror"></textarea>
            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
        </div>


        <div class="justify-content-between">
            <button type="button" wire:click="closeModal" class="btn btn-warning" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">
                {{ $isEditing ? 'Perbarui' : 'Simpan' }}
            </button>
        </div>
    </form>
    <br>
    @endif

    <table class="table table-bordered table-striped datatable1">
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori</th>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($providers as $index => $provider)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $provider->kategori->name ?? '-' }}</td>
                <td>{{ $provider->name }}</td>
                <td>{{ $provider->description }}</td>
                <td>
                    <button wire:click="edit({{ $provider->id }})" class="btn btn-sm btn-warning">Edit</button>
                    <button wire:click="delete({{ $provider->id }})" class="btn btn-sm btn-danger"
                        onclick="confirmDelete({{ $provider->id }})">
                        Hapus
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>