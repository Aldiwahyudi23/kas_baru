<div>
    <div class="mb-4">
        <button wire:click="openModal" class="btn btn-primary">Tambah Kategori</button>
    </div>

    @if ($isModalOpen)
    <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}">
        <div class="form-group">
            <label for="description" class="col-sm-12 col-form-label">Nama <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" wire:model="name"
                class="form-control col-12 @error('name') is-invalid @enderror">
            @error('name')
            <div class="invalid-feedback">
                <strong>{{$message}}</strong>
            </div>
            @enderror
        </div>
        <div class="form-group row">
            <label for="description" class="col-sm-12 col-form-label">Deskripsi <span
                    class="text-danger">*</span></label>
            <div class="col-12">
                <textarea class="form-control col-sm-12 @error('description') is-invalid @enderror" name="description"
                    id="description" wire:model="description"
                    placeholder="Masukan description, contoh: Buat pulsa"></textarea>
                @error('description')
                <div class="invalid-feedback">
                    <strong>{{$message}}</strong>
                </div>
                @enderror
            </div>
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

    <table class="table table-bordered table-striped datatable1 ">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 0;
            ?>
            @foreach ($kategoriKonters as $kategori)
            <?php
            $no++
            ?>
            <tr>
                <td>{{ $no }}</td>
                <td>{{ $kategori->name }}</td>
                <td>{{ $kategori->description }}</td>
                <td>
                    <button wire:click="edit({{ $kategori->id }})" class="btn btn-sm btn-primary">Edit</button>
                    <button wire:click="delete({{ $kategori->id }})" class="btn btn-sm btn-danger">Hapus</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>