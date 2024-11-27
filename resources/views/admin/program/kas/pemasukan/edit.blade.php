@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12">
        <!-- select2bs4 EXAMPLE -->
        <div class="card card-default ">
            <div class="card-header">
                <h3 class="card-title">Edit Data Data pemasukan</h3>

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
                <form action="{{ route('kas-payment.update',Crypt::encrypt($dataKas->id)) }}" method="POST"
                    enctype="multipart/form-data" id="adminForm">
                    @method('PATCH')
                    {{csrf_field()}}

                    <div class="row">
                        <div class="col-12 col-sm-6">

                            <div class="form-group">
                                <label for="payment_date">Tanggal Input</label>
                                <input type="date" name="payment_date" id="payment_date"
                                    value="{{old('payment_date',$dataKas->payment_date)}}"
                                    class="form-control col-12 @error('payment_date') is-invalid @enderror">
                                <span class="text-danger">Tanggal di Konfirmasi {{$dataKas->payment_date}} <br>Jika
                                    tidak mau di ubah, Kosongkan</span>
                            </div>

                            <!-- Pilih Warga -->
                            <div class="form-group">
                                <label for="data_warga_id">Nama Warga <span class="text-danger">*</span></label>
                                <select class="select2bs4 @error('data_warga_id') is-invalid @enderror"
                                    style="width: 100%;" name="data_warga_id" id="data_warga_id">
                                    <option value="">--Pilih Data--</option>
                                    @foreach ($accessProgram as $data)
                                    <option value="{{$data->data_warga_id}}"
                                        {{ old('confirmed_by',$dataKas->data_warga_id) == $data->data_warga_id ? 'selected' : '' }}
                                        @if($data->is_active == 0) disabled @endif>
                                        {{ $data->dataWarga->name }}
                                        @if($data->is_active == 0) (Access Program Tidak Aktif) @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Jumlah Pembayaran -->
                            <div class="form-group">
                                <label for="amount">Jumlah Pembayaran <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" id="amount"
                                    value="{{old('amount',$dataKas->amount)}}"
                                    class="form-control col-12 @error('amount') is-invalid @enderror" disabled>
                                <span class="text-danger">Nominal tidak bisa di rubah</span>
                            </div>

                            <!-- Metode Pembayaran -->
                            <div class="form-group">
                                <label for="payment_method">Metode Pembayaran</label>
                                <select class="select2bs4 @error('payment_method') is-invalid @enderror"
                                    style="width: 100%;" name="payment_method" id="payment_method"
                                    onchange="toggleTransferReceipt()">
                                    <option value="">--Pilih Pembayaran--</option>
                                    <option value="cash"
                                        {{ old('payment_method',$dataKas->payment_method) == 'cash' ? 'selected' : '' }}>
                                        Cash</option>
                                    <option value="transfer"
                                        {{ old('payment_method',$dataKas->payment_method) == 'transfer' ? 'selected' : '' }}>
                                        Transfer</option>
                                </select>
                            </div>

                            <!-- Upload Bukti Transfer (jika metode transfer) -->
                            <div class="form-group" id="transfer_receipt"
                                style="display: {{ old('payment_method',$dataKas->payment_method) == 'transfer' ? 'block' : 'none' }};">
                                <label for="transfer_receipt_path">Upload Bukti Transfer</label>
                                <input type="file" name="transfer_receipt_path" id="transfer_receipt_path"
                                    accept="image/*" value="{{old('payment_method',$dataKas->payment_method)}}"
                                    class="form-control col-12 @error('transfer_receipt_path') is-invalid @enderror"
                                    onchange="preview('.tampil-gambar', this.files[0])">
                                <div class="tampil-gambar mt-3">
                                    @if (isset($dataKas->transfer_receipt_path))
                                    <a href="{{ asset( $dataKas->transfer_receipt_path) }}" data-toggle="lightbox"
                                        data-title="Tanda Bukti Transfer - {{$dataKas->code}}" data-gallery="gallery">
                                        <img src="{{ asset($dataKas->transfer_receipt_path) }}" class="img-fluid mb-2"
                                            alt="white sample" width="100px" />
                                    </a>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="description" class="col-sm-12 col-form-label">Keterangan<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <textarea
                                        class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror"
                                        name="description"
                                        id="description">{{ old('description',$dataKas->description) }}</textarea>
                                </div>
                            </div>

                        </div>
                        <div class="col-12 col-sm-6">
                            <!-- Pilih Warga -->
                            <div class="form-group">
                                <label for="submitted_by">User yang input<span class="text-danger">*</span></label>
                                <select class="select2bs4 @error('submitted_by') is-invalid @enderror"
                                    style="width: 100%;" name="submitted_by" id="submitted_by">
                                    <option value="">--Pilih Data--</option>
                                    @foreach ($accessProgram as $data)
                                    <option value="{{$data->data_warga_id}}"
                                        {{ old('confirmed_by',$dataKas->submitted_by) == $data->data_warga_id ? 'selected' : '' }}
                                        @if($data->is_active == 0) disabled @endif>
                                        {{ $data->dataWarga->name }}
                                        @if($data->is_active == 0) (Tidak Aktif) @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="confirmed_by">Di Setujui Oleh<span class="text-danger">*</span></label>
                                <select class="select2bs4 @error('confirmed_by') is-invalid @enderror"
                                    style="width: 100%;" name="confirmed_by" id="confirmed_by">

                                    <option value="">--Pilih Data--</option>
                                    @foreach ($pengurus_user as $data)
                                    <option value="{{$data->data_warga_id}}"
                                        {{ old('confirmed_by',$dataKas->confirmed_by) == $data->data_warga_id ? 'selected' : '' }}
                                        @if($data->is_active == 0) disabled @endif >
                                        {{ $data->name }}
                                        @if($data->is_active == 0) (Tidak Aktif) @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="status">Satus Transaksi</label>
                                <select class="select2bs4 @error('status') is-invalid @enderror" style="width: 100%;"
                                    name="status" id="status">
                                    <option value="">--Pilih status--</option>
                                    <option value="pending"
                                        {{ old('status',$dataKas->status) == 'pending' ? 'selected' : '' }} disabled>
                                        Pending</option>
                                    <option value="confirmed"
                                        {{ old('status',$dataKas->status) == 'confirmed' ? 'selected' : '' }}>Konfirmasi
                                    </option>
                                    <option value="rejected"
                                        {{ old('status',$dataKas->status) == 'rejected' ? 'selected' : '' }} disabled>
                                        Rejected</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="confirmation_date">Tanggal di Konfirmasi <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="confirmation_date" id="confirmation_date"
                                    value="{{old('confirmation_date',$dataKas->confirmation_date)}}"
                                    class="form-control col-12 @error('confirmation_date') is-invalid @enderror">
                                <span class="text-danger">Tanggal di Konfirmasi {{$dataKas->confirmation_date}} <br>Jika
                                    tidak mau di ubah, Kosongkan</span>
                            </div>


                            <div class="form-group">
                                <label for="is_deposited">Uang di luar</label>
                                <select class="select2bs4 @error('is_deposited') is-invalid @enderror"
                                    style="width: 100%;" name="is_deposited" id="is_deposited">
                                    <option value="">--Pilih Deposito--</option>
                                    <option value="0"
                                        {{ old('is_deposited',$dataKas->is_deposited) == '0' ? 'selected' : '' }}>False
                                    </option>
                                    <option value="1"
                                        {{ old('is_deposited',$dataKas->is_deposited) == '1' ? 'selected' : '' }}>
                                        Deposit</option>
                                </select>
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
        @include('admin.program.kas.pemasukan.tabel')
        <!-- /.card -->
    </div>
</div>
@endsection

@section('script')


@endsection