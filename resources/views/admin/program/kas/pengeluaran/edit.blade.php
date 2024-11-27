@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12">
        <!-- select2bs4 EXAMPLE -->
        <div class="card card-default ">
            <div class="card-header">
                <h3 class="card-title">Edit Data Data Kas</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <form action="{{ route('expenditure.update',Crypt::encrypt($dataEx->id)) }}" method="POST"
                    enctype="multipart/form-data" id="adminForm">
                    @method('PATCH')
                    {{csrf_field()}}

                    <div class="row">
                        <div class="col-12 col-sm-6">

                            <!-- Pilih Warga -->
                            <div class="form-group">
                                <label for="submitted_by">Di Input<span class="text-danger">*</span></label>
                                <select class="select2bs4 @error('submitted_by') is-invalid @enderror"
                                    style="width: 100%;" name="submitted_by" id="submitted_by">
                                    <option value="">--Pilih Pengurus--</option>
                                    @foreach ($pengurus_user as $data)
                                    <option value="{{$data->data_warga_id}}"
                                        {{ old('submitted_by' , $dataEx->submitted_by) == $data->data_warga_id ? 'selected' : '' }}
                                        @if($data->is_active == 0) disabled @endif>
                                        {{ $data->name }}
                                        @if($data->is_active == 0) (Tidak Aktif) @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Pilih Warga -->
                            <div class="form-group">
                                <label for="anggaran_id">Data Anggaran <span class="text-danger">*</span></label>
                                <select class="select2bs4 @error('anggaran_id') is-invalid @enderror"
                                    style="width: 100%;" name="anggaran_id" id="anggaran_id" disabled>
                                    <option value="">--Pilih Anggaran--</option>
                                    @foreach ($anggaran as $data)
                                    <option value="{{$data->id}}"
                                        {{ old('anggaran_id' ,$dataEx->anggaran_id) == $data->id ? 'selected' : ''}}
                                        @if($data->is_active == 0 || $data->name === "Dana Pinjam") disabled @endif>
                                        {{ $data->name }}
                                        @if($data->is_active == 0) (Access Program Tidak Aktif) @endif
                                    </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">Anggaran tidak bisa di rubah</span>
                            </div>

                            <!-- Jumlah Pembayaran -->
                            <div class="form-group">
                                <label for="amount">Jumlah Yang di Keluarkan <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" id="amount"
                                    value="{{old('amount',$dataEx->amount)}}"
                                    class="form-control col-12 @error('amount') is-invalid @enderror" disabled>
                                <span class="text-danger">Nominal tidak bisa di rubah</span>
                            </div>

                            <div class="form-group">
                                <label for="description" class="col-sm-12 col-form-label">Keterangan<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <textarea
                                        class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror"
                                        name="description"
                                        id="description">{{ old('description',$dataEx->description) }}</textarea>
                                </div>
                            </div>

                        </div>
                        <div class="col-12 col-sm-6">
                            <!-- Pilih Warga -->

                            <div class="form-group">
                                <label for="status">Satus Transaksi</label>
                                <select class="select2bs4 @error('status') is-invalid @enderror" style="width: 100%;"
                                    name="status" id="status">
                                    <option value="">--Pilih status--</option>
                                    <option value="pending"
                                        {{ old('status' ,$dataEx->status) == 'pending' ? 'selected' : '' }} disabled>
                                        Pending</option>
                                    <option value="approved_by_chairman"
                                        {{ old('status' ,$dataEx->status) == 'approved_by_chairman' ? 'selected' : '' }}
                                        disabled>Persetujuan Ketua</option>
                                    <option value="disbursed_by_treasurer"
                                        {{ old('status' ,$dataEx->status) == 'disbursed_by_treasurer' ? 'selected' : '' }}
                                        disabled>Proses Pencairan oleh Bendahara</option>
                                    <option value="Acknowledged"
                                        {{ old('status' ,$dataEx->status) == 'Acknowledged' ? 'selected' : '' }}>Di Akui
                                    </option>
                                </select>
                            </div>
                            <!-- Pilih Warga -->
                            <div class="form-group">
                                <label for="approved_by">Persetujuan Ketua<span class="text-danger">*</span></label>
                                <select class="select2bs4 @error('approved_by') is-invalid @enderror"
                                    style="width: 100%;" name="approved_by" id="approved_by">
                                    <option value="">--Pilih Ketua--</option>
                                    @foreach ($pengurus_user as $data)
                                    <option value="{{$data->data_warga_id}}"
                                        {{ old('approved_by' ,$dataEx->approved_by ) == $data->data_warga_id ? 'selected' : ''}}
                                        @if($data->is_active == 0) disabled @endif>
                                        {{ $data->name }}
                                        @if($data->is_active == 0) (Tidak Aktif) @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="approved_date">Tanggal di Konfirmasi Oleh Ketua <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="approved_date" id="approved_date"
                                    value="{{old('approved_date',$dataEx->approved_date)}}"
                                    class="form-control col-12 @error('approved_date') is-invalid @enderror">
                                <span class="text-danger">Tanggal di Konfirmasi {{$dataEx->approved_date}} <br>Jika
                                    tidak mau di ubah, Kosongkan</span>
                            </div>

                            <!-- Pilih Warga -->
                            <div class="form-group">
                                <label for="disbursed_by">Input Pencairan<span class="text-danger">*</span></label>
                                <select class="select2bs4 @error('disbursed_by') is-invalid @enderror"
                                    style="width: 100%;" name="disbursed_by" id="disbursed_by">
                                    <option value="">--Pilih Bendahara--</option>
                                    @foreach ($pengurus_user as $data)
                                    <option value="{{$data->data_warga_id}}"
                                        {{ old('disbursed_by',$dataEx->disbursed_by) == $data->data_warga_id ? 'selected' : ''}}
                                        @if($data->is_active == 0) disabled @endif>
                                        {{ $data->name }}
                                        @if($data->is_active == 0) (Tidak Aktif) @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Upload Bukti Transfer (jika metode transfer) -->
                            <div class="form-group" id="receipt">
                                <label for="receipt_path">Upload Bukti Transfer</label>
                                <input type="file" name="receipt_path" id="receipt_path" accept="image/*"
                                    class="form-control col-12 @error('receipt_path') is-invalid @enderror">
                                @error('receipt_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <span class="text-danger">Jika tidak mau di ubah, Kosongkan</span>
                            </div>

                            <div class="form-group">
                                <label for="disbursed_date">Tanggal Pencairan Oleh Bendahara <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="disbursed_date" id="disbursed_date"
                                    value="{{old('disbursed_date')}}"
                                    class="form-control col-12 @error('disbursed_date') is-invalid @enderror">
                                <span class="text-danger">Tanggal Pencairan {{$dataEx->disbursed_date}} <br>Jika tidak
                                    mau di ubah, Kosongkan</span>
                            </div>

                            <div class="col-6 col-sm-2">
                                <a href="{{asset('storage/'.$dataEx->receipt_path)}}" data-toggle="lightbox"
                                    data-title="Tanda Bukti" data-gallery="gallery">
                                    <img src="{{asset('storage/'.$dataEx->receipt_path)}}" class="img-fluid mb-10"
                                        alt="Tanda Bukti" />
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Update</button>
                </form>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                    <br>- Bertanda bintang Merah wajib di isi.
                </p>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->
<div class="row">
    <div class="col-12">
        <!-- Data ini di ambil dari file terpisah view/admin/master_data/data_admin/tabel -->
        @include('admin.program.kas.pengeluaran.tabel')
        <!-- /.card -->
    </div>
</div>
@endsection

@section('script')


@endsection