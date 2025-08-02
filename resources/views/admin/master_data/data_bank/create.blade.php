<div class="card-body">
    <form action="{{ route('bank-accounts.store') }}" method="POST" enctype="multipart/form-data" id="adminForm">
        @csrf
        <div class="row">
            <div class="col-12 col-sm-6">
                <!-- Pemilik Akun (Warga) -->
                <div class="form-group">
                    <label for="warga_id" class="col-sm-2 col-form-label">Pemilik Akun</label>
                    <select class="form-control select2bs4 @error('warga_id') is-invalid @enderror" style="width: 100%;" name="warga_id" required>
                        <option value="">--Pilih Pemilik Akun--</option>
                        @foreach($wargas as $warga)
                        <option value="{{ $warga->id }}" {{ old('warga_id') == $warga->id ? 'selected' : '' }}>
                            {{ $warga->name }} 
                        </option>
                        @endforeach
                    </select>
                    @error('warga_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Nama Bank -->
                <div class="form-group">
                    <label for="bank_name">Nama Bank</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}" 
                        class="form-control col-12 @error('bank_name') is-invalid @enderror" required>
                    @error('bank_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Nomor Rekening -->
                <div class="form-group">
                    <label for="account_number">Nomor Rekening</label>
                    <input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}" 
                        class="form-control col-12 @error('account_number') is-invalid @enderror" required>
                    @error('account_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Status Aktif -->
                <div class="form-group">
                    <label for="is_active">Status Aktif</label>
                    <select class="form-control col-12 @error('is_active') is-invalid @enderror" name="is_active" required>
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('is_active')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <!-- Nama Pemilik Rekening -->
                <div class="form-group">
                    <label for="account_holder_name">Nama Pemilik Rekening</label>
                    <input type="text" name="account_holder_name" id="account_holder_name" value="{{ old('account_holder_name') }}" 
                        class="form-control col-12 @error('account_holder_name') is-invalid @enderror" required>
                    @error('account_holder_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="form-group">
                    <label for="description" class="col-sm-2 col-form-label">Deskripsi</label>
                    <div class="col-sm-12">
                        <textarea class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror" 
                            name="description" id="description" rows="5">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Button Submit -->
        <div class="form-group row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-success" id="submitBtns">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('bank-accounts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </form>
</div>
<!-- /.card-body -->
<div class="card-footer">
    Catatan: 
    <p>- Pastikan nomor rekening belum terdaftar sebelumnya</p>
    <p>- Nama pemilik rekening harus sesuai dengan buku tabungan</p>
</div>