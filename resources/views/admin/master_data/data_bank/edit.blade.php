@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->
<div class="row">
    <div class="col-12">
        <!-- select2bs4 EXAMPLE -->
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Edit Data Rekening Bank</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <form action="{{ route('bank-accounts.update', $bankAccount->id) }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @method('PUT')
                    @csrf

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <!-- Pemilik Akun (Warga) -->
                            <div class="form-group">
                                <label for="warga_id" class="col-sm-2 col-form-label">Pemilik Akun</label>
                                <select class="form-control select2bs4 @error('warga_id') is-invalid @enderror" style="width: 100%;" name="warga_id" required>
                                    @if(old('warga_id', $bankAccount->warga_id))
                                    <option selected="selected" value="{{ old('warga_id', $bankAccount->warga_id) }}">
                                        {{ $bankAccount->warga->nama }} ({{ $bankAccount->warga->nik ?? '-' }})
                                    </option>
                                    @endif
                                    <option value="">--Pilih Pemilik Akun--</option>
                                    @foreach($wargas as $warga)
                                    <option value="{{ $warga->id }}" {{ old('warga_id', $bankAccount->warga_id) == $warga->id ? 'selected' : '' }}>
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
                                <input type="text" name="bank_name" id="bank_name" 
                                    value="{{ old('bank_name', $bankAccount->bank_name) }}" 
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
                                <input type="text" name="account_number" id="account_number" 
                                    value="{{ old('account_number', $bankAccount->account_number) }}" 
                                    class="form-control col-12 @error('account_number') is-invalid @enderror" required>
                                @error('account_number')
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
                                <input type="text" name="account_holder_name" id="account_holder_name" 
                                    value="{{ old('account_holder_name', $bankAccount->account_holder_name) }}" 
                                    class="form-control col-12 @error('account_holder_name') is-invalid @enderror" required>
                                @error('account_holder_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Status Aktif -->
                            <div class="form-group">
                                <label for="is_active">Status Aktif</label>
                                <select class="form-control col-12 @error('is_active') is-invalid @enderror" name="is_active" required>
                                    <option value="1" {{ old('is_active', $bankAccount->is_active) == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('is_active', $bankAccount->is_active) == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('is_active')
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
                                        name="description" id="description" rows="5">{{ old('description', $bankAccount->description) }}</textarea>
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
                                <i class="fas fa-save"></i> Update
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
                <p>- Pastikan data yang diubah sudah benar dan valid</p>
                <p>- Nomor rekening harus unik dan tidak boleh duplikat</p>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->
<div class="row">
    <div class="col-12">
        <!-- Tampilkan tabel data bank account -->
        @include('admin.master_data.data_bank.tabel')
        <!-- /.card -->
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi select2
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        });

        // Inisialisasi summernote
        $('.summernote-textarea').summernote({
            height: 150,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']]
            ]
        });
    });
</script>
@endsection

@section('styles')
<style>
    .small-text {
        font-size: 12px;
        color: red;
        display: block;
        margin-top: 5px;
    }
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.25rem + 2px) !important;
    }
</style>
@endsection