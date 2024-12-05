@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12">
        <!-- select2bs4 EXAMPLE -->
        <div class="card card-default ">
            <div class="card-header">
                <h3 class="card-title">Edit Data Konter</h3>

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
                <form action="{{ route('konter-transaksi.update',Crypt::encrypt($data_transaksi->id)) }}" method="POST"
                    enctype="multipart/form-data" id="adminForm">
                    @method('PATCH')
                    {{csrf_field()}}

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Kode Transaksi</label>
                                <input type="text" class="form-control" id="code" name="code"
                                    value="{{ old('code',$data_transaksi->code) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Produk</label>
                                <select class="form-control" id="product_id" name="product_id">
                                    <option value="">--Pilih Produk--</option>
                                    @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ old('product_id',$data_transaksi->product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->kategori->name }} {{ $product->provider->name }}
                                        {{number_format($product->amount,0,',','.' )}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="submitted_by" class="form-label">Diajukan Oleh</label>
                                <input type="text" class="form-control" id="submitted_by" name="submitted_by"
                                    value="{{ old('submitted_by',$data_transaksi->submitted_by) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Metode Pembayaran</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option value="">--Pilih Pembayaran--</option>
                                    <option value="cash"
                                        {{ old('payment_method',$data_transaksi->payment_method) == 'cash' ? 'selected' : '' }}>
                                        Cash
                                    </option>
                                    <option value="transfer"
                                        {{ old('payment_method',$data_transaksi->payment_method) == 'transfer' ? 'selected' : '' }}>
                                        Transfer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label for="buying_price" class="form-label">Harga Beli</label>
                                <input type="number" step="0.01" class="form-control" id="buying_price"
                                    name="buying_price" value="{{ old('buying_price',$data_transaksi->buying_price) }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga Jual</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price"
                                    value="{{ old('price',$data_transaksi->price) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="pending"
                                        {{ old('status',$data_transaksi->status) == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="Berhasil"
                                        {{ old('status',$data_transaksi->status) == 'Berhasil' ? 'selected' : '' }}>
                                        Berhasil</option>
                                    <option value="Gagal"
                                        {{ old('status',$data_transaksi->status) == 'Gagal' ? 'selected' : '' }}>Gagal
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="is_deposited" class="form-label">Deposit</label>
                                <select class="form-control" id="is_deposited" name="is_deposited" required>
                                    <option value="">--pilih deposite--</option>
                                    <option value="1"
                                        {{ old('is_deposited',$data_transaksi->is_deposited) == '1' ? 'selected' : '' }}>
                                        Ya</option>
                                    <option value="0"
                                        {{ old('is_deposited',$data_transaksi->is_deposited) == '0' ? 'selected' : '' }}>
                                        Tidak</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="deadline_date" class="form-label">Tanggal Batas Waktu</label>
                                <input type="date" class="form-control" id="deadline_date" name="deadline_date"
                                    value="{{ old('deadline_date',$data_transaksi->deadline_date) }}">
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
        @include('admin.konter.transaksi.tabel')
        <!-- /.card -->
    </div>
</div>
@endsection

@section('script')


@endsection

@section('style')
<style>
.small-text {
    font-size: 12px;
    color: red;
    display: block;
    /* Aggar label di bawah input */
    margin-top: 5px;
}
</style>

@endsection