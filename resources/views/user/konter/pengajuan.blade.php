@extends('user.layout.app')

@section('content')

@if ($pengajuan->status == "pending")
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Penting !</h5>
    Harap Konfirmasi dengan benar, pastikan data sesuai karena setelah di konfirmasi maka akan masuk ke data dan akan
    masuk ke perhitungan saldo.
</div>
@endif
@if ($pengajuan->status == "Berhasil")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Data pembayaran kas sudah di konfirmasi, dan sudah masuk ke data.
</div>
@endif
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">{{$pengajuan->product->kategori->name}} {{$pengajuan->product->provider->name}} (
            {{number_format($pengajuan->product->amount,0,',','.')}} ) <br>
            @if($pengajuan->status === 'Selesai')
            <span class="badge badge-success">Selesai</span>
            @elseif($pengajuan->status === 'Berhasil')
            <span class="badge badge-info">Berhasil</span>
            @elseif($pengajuan->status === 'Proses')
            <span class="badge badge-warning">Proses</span>
            @elseif($pengajuan->status === 'Gagal')
            <span class="badge badge-danger">Gagal</span>
            @elseif($pengajuan->status === 'pending')
            <span class="badge badge-secondary">Pending</span>
            @else
            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
            @endif
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>


    @if ($pengajuan->payment_status == "Hutang")
    @if ($pengajuan->status == "Berhasil")
    <div class="alert 
        @if($remaining_time <= 1 ) alert-danger 
        @else alert-warning 
        @endif alert-dismissible">
        <center>
            @if($remaining_time == 0) Jatuh Tempo
            @elseif($remaining_time <= -1) Lewat {{ $remaining_time }} hari @else {{ $remaining_time }} hari Lagi @endif
                </center>
    </div>
    @endif
    @endif

    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body p-0">
            <table class="table table-hover text-nowrap table-responsive">
                <tbody>
                    <tr>
                        <td>Kode</td>
                        <td>:</td>
                        <td>{{$pengajuan->code}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>:</td>
                        <td>{{$pengajuan->created_at}}</td>
                    </tr>
                    <tr>
                        <td>Di Input oleh</td>
                        <td>:</td>
                        <td>{{$pengajuan->submitted_by}}</td>
                    </tr>

                    <tr>
                        <td>Status / Tempo</td>
                        <td>:</td>
                        <td>
                            @if($pengajuan->payment_status === 'Langsung')
                            <span class="badge badge-success">Langsung</span>
                            @elseif($pengajuan->payment_status === 'Hutang')
                            <span class="badge badge-info">Hutang</span>
                            @else
                            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                            @endif
                            <br>
                            {{$pengajuan->deadline_date}}
                        </td>
                    </tr>
                    <tr>
                        <td>No Tujuan</td>
                        <td>:</td>
                        <td>
                            <input type="text" id="tujuan" value=" {{$pengajuan->detail->no_hp ?? ''}}"
                                oninput="syncPhoneNumber(this)">
                        </td>
                    </tr>
                    @if ($pengajuan->product->kategori->name == "Listrik")
                    <tr>
                        <td>No Listrik</td>
                        <td>:</td>
                        <td>{{$pengajuan->detail->no_listrik}}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Atas Nama</td>
                        <td>:</td>
                        <td>{{$pengajuan->detail->name}}</td>
                    </tr>
                </tbody>
            </table>
            <div class="card-footer">
                <p>
                    Harga Jual :
                </p>
                <h3> Rp {{number_format($pengajuan->price,0,',','.')}} </h3>

                <p>Harga Jual diatas adalah Harga Jual berdasarkan pemilihan tenor Waktu</p>
                <p>Keterangan : <br>
                    {!!$pengajuan->detail->description!!}
                </p>
            </div>
        </div>

        @if (in_array($pengajuan->status, ['pending','Proses','Berhasil']))
        @if(Auth::user()->role->name == "Bendahara" || Auth::user()->role->name == "Wakil Bendahara" ||
        Auth::user()->role->name == "Sekretaris" || Auth::user()->role->name == "Wakil Sekretaris" ||
        Auth::user()->role->name == "Ketua" || Auth::user()->role->name == "Wakil Ketua")
        <form action="{{ route('konter.berhasil',Crypt::encrypt($pengajuan->id)) }}" method="POST"
            enctype="multipart/form-data" id="adminForm">
            @method('PATCH')
            {{csrf_field()}}

            <input type="hidden" id="no_hp" name="no_hp" value="{{ old('no_hp') ?? '' }}">

            @if ($pengajuan->status == "Proses"|| $pengajuan->status == "pending")
            <!-- Jumlah Pembayaran -->
            <div class="form-group">
                <label for="buying_price">Harga Beli <span class="text-danger">*</span></label>
                <input type="text" name="buying_price_display" id="buying_price_display"
                    value="{{ old('buying_price',$pengajuan->product->buying_price) ? number_format(old('buying_price',$pengajuan->product->buying_price), 0, ',', '.') : '' }}"
                    class="form-control col-12 @error('buying_price') is-invalid @enderror"
                    placeholder="Sesuaikan Harga dengan Transaksi" oninput="ID(this)">
                <input type="hidden" name="buying_price" id="buying_price" value="{{ old('buying_price') }}">
            </div>
            <!-- Jumlah Pembayaran -->
            <div class="form-group">
                <label for="diskon">diskon</label>
                <input type="text" name="diskon_display" id="diskon_display"
                    value="{{ old('diskon') ? number_format(old('diskon'), 0, ',', '.') : '0' }}"
                    class="form-control col-12 @error('diskon') is-invalid @enderror"
                    placeholder="Sesuaikan Harga dengan Transaksi" oninput="IDdiskon(this)" onfocus="clearZero(this)"
                    onblur="restoreZero(this)">
                <input type="hidden" name="diskon" id="diskon" value="{{ old('diskon') }}">
            </div>

            @if ($pengajuan->product->provider->name == "Token Listrik")
            <div class="form-group">
                <label for="token_code">Token Listrik<span class="text-danger">*</span></label>
                <input type="text" name="token_code" id="token_code" value="{{ old('token_code')}}"
                    class="form-control col-12 @error('token_code') is-invalid @enderror"
                    placeholder="Masukan Tokennya">

            </div>
            @endif
            @if ($pengajuan->product->provider->name == "Tagihan Listrik")
            <div class="form-group">
                <label for="price_display">Nominal Tagihan</label>
                <input type="text" name="price_display" id="price_display" value="{{ old('price_display')}}"
                    class="form-control col-12 @error('price_display') is-invalid @enderror"
                    placeholder="Masukan nominal Tagihan " disabled>
                <input type="hidden" name="price" id="price" value="{{old('price')}}">
            </div>
            @endif

            @if ($pengajuan->payment_status == "Hutang" )
            <div class="form-group">
                <label for="status">Satus Konfirmasi<span class="text-danger">*</span></label>
                <select class="select2bs4 @error('status') is-invalid @enderror" style="width: 100%;" name="status"
                    id="status">
                    <option value="Berhasil" {{ old('status',$pengajuan->status) == 'Berhasil' ? 'selected' : '' }}>
                        Pulsa sudah Berhasil di kirim
                    </option>
                    <option value="pending" {{ old('status',$pengajuan->status) == 'pending' ? 'selected' : '' }}>
                        Pending</option>
                    <option value="Gagal" {{ old('status',$pengajuan->status) == 'Gagal' ? 'selected' : '' }}>
                        Batal / Gagal</option>
                </select>
            </div>
            @endif
            @endif

            <!-- Input ini tampil hanya ketika  -->
            @if ($pengajuan->status == "Berhasil")
            <!-- Jumlah Pembayaran -->
            <div class="form-group">
                <label for="invoice">Tagihan yang di Bayar <span class="text-danger">*</span></label>
                <input type="text" name="invoice_display" id="invoice_display"
                    value="{{ old('invoice',$pengajuan->invoice) ? number_format(old('invoice',$pengajuan->invoice), 0, ',', '.') : '' }}"
                    class="form-control col-12 @error('invoice') is-invalid @enderror"
                    placeholder="Sesuaikan Harga dengan Transaksi" oninput="Invoice(this)">
                <input type="hidden" name="invoice" id="invoice" value="{{ old('invoice',$pengajuan->invoice) }}">
                <input type="hidden" name="diskon" id="diskon" value="{{$pengajuan->diskon}}">
                <input type="hidden" name="buying_price" id="buying_price" value="{{$pengajuan->buying_price}}">
            </div>
            @endif

            <!-- Form input ini tampil ketika pembayaran secara langsung dan status berhasil atau sudah pembelian pulsa -->
            @if ($pengajuan->status == "Berhasil" || $pengajuan->payment_status == "Langsung")
            <!-- Metode Pembayaran -->
            <div class="form-group">
                <label for="payment_method">Metode Pembayaran</label>
                <span class="text-danger">*</span></label>
                <select class="select2bs4 @error('payment_method') is-invalid @enderror" style="width: 100%;"
                    name="payment_method" id="payment_method" onchange="togglePembayaran()">
                    <option value="">--Pilih Pembayaran--</option>
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash
                    </option>
                    <option value=" transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>
                        Transfer</option>
                </select>
            </div>

            <!-- Upload Bukti Transfer (jika metode transfer) -->
            <div class="form-group" id="transfer_receipt"
                style="display: {{ old('payment_method') == 'cash' ? 'block' : 'none' }};">
                <label for="warga_id">Uang Di Pegang siapa</label>
                <select class="select2bs4 @error('warga_id') is-invalid @enderror" style="width: 100%;" name="warga_id"
                    id="warga_id">
                    <option value="">--Pilih Anggota--</option>
                    @foreach ($data_Warga as $data)
                    <option value="{{$data->id}}" {{ old('warga_id') == $data->id ? 'selected' : '' }}>
                        {{$data->name}}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group row">
                <label for="description" class="col-sm-12 col-form-label">Catatan Pengurus
                    <textarea class="form-control col-12 @error('description') is-invalid @enderror" name="description"
                        id="description"
                        placeholder="Masukan Catatan Tambahan Jika ada ">{{ old('description') }}</textarea>
            </div>

            @if ($pengajuan->payment_status == "Langsung")
            <div class="form-group">
                <select class="select2bs4 @error('status') is-invalid @enderror" style="width: 100%;" name="status"
                    id="status">
                    <option value="Selesai" {{ old('status',$pengajuan->status) == 'Selesai' ? 'selected' : '' }}>
                        Selesai
                    </option>
                    <option value="pending" {{ old('status',$pengajuan->status) == 'pending' ? 'selected' : '' }}>
                        Pending</option>
                    <option value="Gagal" {{ old('status',$pengajuan->status) == 'Gagal' ? 'selected' : '' }}>
                        Batal / Gagal</option>
                </select>
            </div>
            @endif
            @if ($pengajuan->status == "Berhasil")
            <input type="hidden" name="status" value="Selesai">
            @endif

            <!-- Untuk status yang pembayarannya langsung atau selesai -->

            @endif
            <!-- Button Submit -->
            <button type="submit" class="btn btn-success" id="submitBtns">kirim</button>
        </form>
        @endif
        @endif
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <p>
            Catatan : <br>
            - Segera lakukan Transaksi Agar Pulsa masuk <br>
            - Pastikan data sesuai <br>
        </p>
        <p><i> Pilih Pending jika data ada yang tidak sesuai dan sementara sedang di tinjau <br>
                Pilih Batal jika data tidak sesuai atau gagal</i></p>
    </div>
</div>
<!-- /.card -->


@endsection

<script>
    let originalBuyingPrice = 0; // Variabel global untuk menyimpan nilai asli
    // Pastikan variabel global untuk nilai awal dari controller
    let additionalPrice = parseFloat("{{ $pengajuan->price ?? 0 }}") || 0;

    function ID(element) {
        // Ambil nilai asli tanpa format
        let rawValue = element.value.replace(/\./g, '').replace(',', '.');

        // Pastikan hanya angka dan desimal yang valid
        if (!/^\d*(\.\d{0,2})?$/.test(rawValue)) {
            rawValue = element.dataset.previousValue || '';
        }

        // Simpan nilai sebelumnya untuk validasi selanjutnya
        element.dataset.previousValue = rawValue;

        // Format angka sesuai Indonesia (titik ribuan, koma desimal)
        const [integer, decimal] = rawValue.split('.');
        const formattedInteger = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        const formattedValue = decimal !== undefined ?
            `${formattedInteger},${decimal.slice(0, 2)}` :
            formattedInteger;

        element.value = formattedValue;

        // Simpan nilai asli (tanpa format) ke input hidden
        document.getElementById('buying_price').value = rawValue;
        // Simpan nilai asli ke variabel global untuk digunakan dalam penghitungan diskon
        originalBuyingPrice = parseFloat(rawValue) || 0;

        // Tampilkan nilai awal di buying_price_display
        document.getElementById('buying_price_display').value = formattedValue;

        // Hitung nilai final untuk price_display
        const finalValue = (parseFloat(rawValue) || 0) + additionalPrice;

        // Format nilai final
        const [finalInteger, finalDecimal] = finalValue.toFixed(0).split('.');
        const formattedFinalInteger = finalInteger.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        const formattedFinalValue = finalDecimal !== undefined ?
            `${formattedFinalInteger},${finalDecimal.slice(0, 2)}` :
            formattedFinalInteger;


        // Simpan nilai asli (tanpa format) ke input hidden
        document.getElementById('price').value = finalValue;

        // Tampilkan nilai awal di price_display
        document.getElementById('price_display').value = formattedFinalValue;

    }

    //membuat input diskon jadi bernilai nol
    function clearZero(element) {
        if (element.value === '0') {
            element.value = ''; // Hapus nilai jika 0
        }
    }

    function IDdiskon(element) {
        // Ambil nilai asli tanpa format
        let rawValue = element.value.replace(/\./g, '').replace(',', '.');

        // Pastikan hanya angka dan desimal yang valid
        if (!/^\d*(\.\d{0,2})?$/.test(rawValue)) {
            rawValue = element.dataset.previousValue || '';
        }

        // Simpan nilai sebelumnya untuk validasi selanjutnya
        element.dataset.previousValue = rawValue;

        // Format angka sesuai Indonesia (titik ribuan, koma desimal)
        const [integer, decimal] = rawValue.split('.');
        const formattedInteger = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        const formattedValue = decimal !== undefined ?
            `${formattedInteger},${decimal.slice(0, 2)}` :
            formattedInteger;

        element.value = formattedValue;

        document.getElementById('diskon').value = rawValue;

        // Lakukan penghitungan harga setelah diskon
        const discountValue = parseFloat(rawValue) || 0;
        const updatedPrice = Math.max(originalBuyingPrice - discountValue, 0); // Pastikan hasil tidak negatif

        // Format hasil penghitungan dan tampilkan di buying_price_display
        const [updatedInteger, updatedDecimal] = updatedPrice.toFixed(0).split('.');
        const formattedUpdatedInteger = updatedInteger.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        const formattedUpdatedValue = updatedDecimal !== undefined ?
            `${formattedUpdatedInteger},${updatedDecimal}` :
            formattedUpdatedInteger;

        document.getElementById('buying_price_display').value = formattedUpdatedValue;
        document.getElementById('buying_price').value = updatedPrice;

        // Reset ke nilai awal jika diskon kosong
        if (rawValue === '') {
            const [originalInteger, originalDecimal] = originalBuyingPrice.toFixed(0).split('.');
            const formattedOriginalInteger = originalInteger.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            const formattedOriginalValue = originalDecimal !== undefined ?
                `${formattedOriginalInteger},${originalDecimal}` :
                formattedOriginalInteger;

            document.getElementById('buying_price_display').value = formattedOriginalValue;
            document.getElementById('buying_price').value = updatedPrice;
        }

    }

    //Untuk menyambungkan data input No hp yang ada di luar fom 
    function syncPhoneNumber(element) {
        // Sinkronisasi nilai dari input luar ke input di dalam form
        document.getElementById('no_hp').value = element.value;

    }

    function Invoice(element) {
        // Ambil nilai asli tanpa format
        let rawValue = element.value.replace(/\./g, '').replace(',', '.');

        // Pastikan hanya angka dan desimal yang valid
        if (!/^\d*(\.\d{0,2})?$/.test(rawValue)) {
            rawValue = element.dataset.previousValue || '';
        }

        // Simpan nilai sebelumnya untuk validasi selanjutnya
        element.dataset.previousValue = rawValue;

        // Format angka sesuai Indonesia (titik ribuan, koma desimal)
        const [integer, decimal] = rawValue.split('.');
        const formattedInteger = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        const formattedValue = decimal !== undefined ?
            `${formattedInteger},${decimal.slice(0, 2)}` :
            formattedInteger;

        element.value = formattedValue;

        // Simpan nilai asli (tanpa format) ke input hidden
        document.getElementById('invoice').value = rawValue;
        // Simpan nilai asli ke variabel global untuk digunakan dalam penghitungan diskon
        originalBuyingPrice = parseFloat(rawValue) || 0;

        // Tampilkan nilai awal di invoice_display
        document.getElementById('invoice_display').value = formattedValue;
    }




    // Inisialisasi ulang format saat halaman dimuat (untuk nilai lama)
    document.addEventListener('DOMContentLoaded', function() {
        const displayInput = document.getElementById('buying_price_display');
        if (displayInput && displayInput.value) {
            ID(displayInput);
        }

        const displayInputDiskon = document.getElementById('diskon_display');
        if (displayInputDiskon && displayInputDiskon.value) {
            IDdiskon(displayInputDiskon);
        }
        const displayInputNoHP = document.getElementById('no_hp');
        if (displayInputNoHP && displayInputNoHP.value) {
            syncPhoneNumber(displayInputNoHP);
        }

    });
</script>

<!-- Untuk memunculkan Input tanda bukti Tf -->
<script>
    // Function to toggle the visibility of the transfer receipt input based on selected payment method
    function togglePembayaran() {
        var paymentMethod = document.getElementById('payment_method').value;
        var transferReceipt = document.getElementById('transfer_receipt');
        transferReceipt.style.display = (paymentMethod === 'cash') ? 'block' : 'none';
    }
</script>