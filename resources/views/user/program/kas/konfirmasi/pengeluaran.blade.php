@extends('user.layout.app')

@section('content')

@if ($pengeluaran->status != "Acknowledged")
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Penting !</h5>
    Harap Konfirmasi dengan benar, data pengeluaran yang sudah diAjukan harus berisi keterangan yang detail untuk sebuah
    laporan.
</div>
@endif
@if ($pengeluaran->status == "Acknowledged")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Data Pengeluaran sudah di keluarkan sesuai data di bawah.
</div>
@endif
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">{{$pengeluaran->anggaran->name}} ( {{$pengeluaran->code}} )</h3>
        <div class="card-tools">
            @if ($pengeluaran->status == "approved_by_chairman")
            <a class="btn btn-tool" href="{{route('pengeluaran.edit',Crypt::encrypt($pengeluaran->id))}}">
                <i class="fas fa-pencil-alt">
                </i>
            </a>
            <a class="btn btn-tool" href="{{route('pengeluaran.destroy',Crypt::encrypt($pengeluaran->id))}}"
                data-confirm-delete="true">
                <i class="fas fa-trash">
                </i>
            </a>
            @endif
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body  p-0">
            <table class="table table-hover text-nowrap table-responsive">
                <tbody>
                    <tr>
                        <td>Kode</td>
                        <td>:</td>
                        <td>{{$pengeluaran->code}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>:</td>
                        <td>{{$pengeluaran->created_at}}</td>
                    </tr>
                    <tr>
                        <td>Nama Anggaran</td>
                        <td>:</td>
                        <td>{{$pengeluaran->anggaran->name}}</td>
                    </tr>
                    <tr>
                        <td>Di Input oleh</td>
                        <td>:</td>
                        <td>{{$pengeluaran->sekretaris->name}}</td>
                    </tr>
                    <tr>
                        <td>Nominal</td>
                        <td>:</td>
                        <td>Rp. {{number_format( $pengeluaran->amount, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>
                            @if($pengeluaran->status === 'Acknowledged')
                            <span class="badge badge-success">Selesai</span>
                            @elseif($pengeluaran->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                            @elseif($pengeluaran->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                            @elseif($pengeluaran->status === 'approved_by_chairman')
                            <span class="badge badge-secondary">Menunggu persetujuan Ketua</span>
                            @elseif($pengeluaran->status === 'disbursed_by_treasurer')
                            <span class="badge badge-secondary">Dalam Proses Pencairan</span>
                            @else
                            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                            @endif
                        </td>
                    </tr>
                    @if ($pengeluaran->status == "Acknowledged" || $pengeluaran->status == "disbursed_by_treasurer")
                    <tr>
                        <td>Di Konfirmasi Oleh</td>
                        <td>:</td>
                        <td>{{ $pengeluaran->ketua->name}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal di Konfirmasi</td>
                        <td>:</td>
                        <td>{{ $pengeluaran->approved_date}}</td>
                    </tr>
                    @endif
                    @if ($pengeluaran->status == "Acknowledged")
                    <tr>
                        <td>Pencaian</td>
                        <td>:</td>
                        <td>{{ $pengeluaran->bendahara->name}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal di cairkan</td>
                        <td>:</td>
                        <td>{{ $pengeluaran->disbursed_date}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="card-footer">
                <p>
                    Keterangan : <br>
                    {!!$pengeluaran->description!!}
                </p>
            </div>
            <br>
            <!-- Thumbnail Tanda Bukti Transfer -->
            @if ($pengeluaran->receipt_path)
            <div class="form-group col-6 col-sm-2 justify-between">
                <a href="{{ asset($pengeluaran->receipt_path) }}" data-toggle="lightbox"
                    data-title="Tanda Bukti Transfer - {{$pengeluaran->code}}" data-gallery="gallery">
                    <img src="{{ asset($pengeluaran->receipt_path) }}" class="img-fluid mb-2" alt="white sample" />
                </a>
            </div>
            @endif
        </div>
        @if ($pengeluaran->status != "Acknowledged")
        <!-- untuk ketua -->
        @if(Auth::user()->role->name == "Ketua" || Auth::user()->role->name == "Wakil Ketua")
        @if ($pengeluaran->status == "approved_by_chairman")
        <form action="{{ route('pengeluaran.approved',Crypt::encrypt($pengeluaran->id)) }}" method="POST"
            enctype="multipart/form-data" id="adminForm">
            @method('PATCH')
            {{csrf_field()}}
            <input type="hidden" name="status" value="disbursed_by_treasurer">

            <!-- Button Submit -->
            <button type="submit" class="btn btn-success" id="submitBtns">Setujui</button>
        </form>
        @else
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-check"></i> Proses Pencairan</h5>
            - Data Pengeluaran sudah masuk di bendahara dan akan segera di cairkan oleh bendahara.<br>
            - Untuk mempercepat proses pencairan, harap di bantu untuk menghubungi Bendahara
        </div>
        @endif
        @endif

        <!-- Untuk halaman Bendahara pencairan -->
        <!-- Jika sudah di konfirmasi ketua -->
        @if(Auth::user()->role->name == "Bendahara" || Auth::user()->role->name == "Wakil Bendahara")
        @if ($pengeluaran->status == "disbursed_by_treasurer")
        <form action="{{ route('pengeluaran.disbursed',Crypt::encrypt($pengeluaran->id)) }}" method="POST"
            enctype="multipart/form-data" id="adminForm">
            @method('PATCH')
            {{csrf_field()}}
            <input type="hidden" name="amount" value="{{$pengeluaran->amount}}">
            <input type="hidden" name="anggaran_id" value="{{$pengeluaran->anggaran_id}}">
            <input type="hidden" name="disbursed_date" value="bendahara">
            <input type="hidden" name="status" value="Acknowledged">

            <div class="form-group">
                <label for="receipt_path">Upload Tanda Bukti <span class="text-danger">*</span></label>
                <input type="file" name="receipt_path" id="receipt_path" value="{{old('receipt_path')}}"
                    class="form-control col-12 @error('foto') is-invalid @enderror" required
                    onchange="preview('.tampil-gambar', this.files[0])">
                <br>
                <div class="tampil-gambar"></div>
            </div>

             {{-- Untuk menamngkap data Bank accounbt --}}
            <div class="form-group">
                  <label for="payment_method">Rekening Bank Sumber </label>
                <span class="text-danger">*</span></label>
                <select class="select2bs4 @error('bank_account_id') is-invalid @enderror" style="width: 100%;" name="bank_account_id" id="bank_account_id" required>
                    <option value="">-- Pilih Rekening Bank --</option>
                    @foreach($bankAccounts as $account)
                    <option value="{{ $account->id }}" {{ old('bank_account_id', isset($selectedAccount) ? $selectedAccount->id : '') == $account->id ? 'selected' : '' }}>
                        {{ $account->bank_name }} - {{ $account->account_number }} ({{ $account->account_holder_name }})
                    </option>
                    @endforeach
                </select>
                @error('bank_account_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="col-sm-12 col-form-label">Keterangan Tambahan</label>
                <div class="col-sm-12">
                    <textarea class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror"
                        name="description" id="description">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Button Submit -->
            <button type="submit" class="btn btn-success" id="submitBtns">Upload Tanda Bukti</button>
        </form>
        <!-- JIka masih belum di setujui ketua tampilkan ini  -->
        @else
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-exclamation-triangle"></i> Menunggu persetujuan Ketua </h5>
            Belum bisa melakukan pencairan, harap tunggu info setelah ketua menyetujui
        </div>
        @endif
        @endif
        @endif
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <p>
            Catatan : <br>
            - Data yang di input di sesuaikan dengan anggaran yang sudah di sepakati <br>
            - Pastikan data sesuai <br>
        </p>
        <p><i>Anggaran yang sudah di ajukan semoga bermanfaat</i></p>
    </div>
</div>
<!-- /.card -->


@endsection