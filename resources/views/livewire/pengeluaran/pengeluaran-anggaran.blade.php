<div>
    <!-- Dropdown untuk memilih anggaran -->

    <div class="form-group">
        <label for="anggaran_id">Data Anggaran <span class="text-danger">*</span></label>
        <select class="form-control @error('anggaran_id') is-invalid @enderror" name="anggaran_id"
            wire:model="selectedAnggaran">
            <option value="">--Pilih Anggaran--</option>
            @foreach ($anggaran as $data)
            <option value="{{ $data->id }}" @if( $data->is_active == 0 || $data->name ===
                "Dana Pinjam")
                disabled @endif>
                {{ $data->name }}
                @if($data->is_active == 0)
                (Access Program Tidak Aktif)
                @endif
                @if ($anggaranStatus[$data->id])
                ({{ $anggaranStatus[$data->id] }})
                @endif
            </option>
            @endforeach
        </select>
    </div>
</div>