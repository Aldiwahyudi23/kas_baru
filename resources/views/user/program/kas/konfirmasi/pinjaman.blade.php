@extends('user.layout.app')

@section('content')

@if ($pinjaman->status != "Acknowledged")
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Penting !</h5>
    Harap Konfirmasi dengan benar, data pinjaman yang sudah diAjukan harus berisi keterangan yang detail untuk sebuah
    laporan.
</div>
@endif
@if ($pinjaman->status == "Acknowledged")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Data Pengeluaran sudah di keluarkan sesuai data di bawah.
</div>
@endif
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">{{$pinjaman->anggaran->name}} ( {{$pinjaman->code}} )</h3>
        <div class="card-tools">
            @if (in_array($pinjaman->status, ['pending', 'approved_by_chairman']))
            <a class="btn btn-tool" href="{{route('pinjaman.editPengurus',Crypt::encrypt($pinjaman->id))}}">
                <i class="fas fa-pencil-alt">
                </i>
            </a>
            <a class="btn btn-tool" href="{{route('pinjaman.destroyPengurus',Crypt::encrypt($pinjaman->id))}}"
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
                        <td>{{$pinjaman->code}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>:</td>
                        <td>{{$pinjaman->created_at}}</td>
                    </tr>
                    <tr>
                        <td>Nama Anggaran</td>
                        <td>:</td>
                        <td>{{$pinjaman->anggaran->name}}</td>
                    </tr>
                    <tr>
                        <td>Di Input oleh</td>
                        <td>:</td>
                        <td>{{$pinjaman->sekretaris->name}}</td>
                    </tr>
                    <tr>
                        <td>Data Warga</td>
                        <td>:</td>
                        <td>{{$pinjaman->warga->name}}</td>
                    </tr>
                    <tr>
                        <td>Nominal</td>
                        <td>:</td>
                        <td>Rp. {{number_format( $pinjaman->loan_amount, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>
                            @if($pinjaman->status === 'Acknowledged')
                            <span class="badge badge-success">Uang sudah diterima</span>
                            @elseif($pinjaman->status === 'pending')
                            <span class="badge badge-warning">Menunggu persetujuan Ketua</span>
                            @elseif($pinjaman->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                            @elseif($pinjaman->status === 'approved_by_chairman')
                            <span class="badge badge-secondary">Proses Pencairan oleh Bendahara</span>
                            @elseif($pinjaman->status === 'In Repayment')
                            <span class="badge badge-success">Proses Cicil</span>
                            @elseif($pinjaman->status === 'Paid in Full')
                            <span class="badge badge-success">Selesai / Lunas</span>
                            @elseif($pinjaman->status === 'disbursed_by_treasurer')
                            <span class="badge badge-secondary">Sudah di cairkan, <br> Menunggu konfirmasi bahwa uang
                                telah
                                di terima </span>
                            @else
                            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                            @endif
                        </td>
                    </tr>
                    @if ($pinjaman->status != "pending" )
                    <tr>
                        <td>Di Konfirmasi Oleh</td>
                        <td>:</td>
                        <td>{{ $pinjaman->ketua->name}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal di Konfirmasi</td>
                        <td>:</td>
                        <td>{{ $pinjaman->approved_date}}</td>
                    </tr>
                    @endif
                    @if ($pinjaman->status == "disbursed_by_treasurer")
                    <tr>
                        <td>Pencaian</td>
                        <td>:</td>
                        <td>{{ $pinjaman->bendahara->name}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal di cairkan</td>
                        <td>:</td>
                        <td>{{ $pinjaman->disbursed_date}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="card-footer">
                <p>
                    Keterangan : <br>
                    {!!$pinjaman->description!!}
                </p>
            </div>
            <br>
            <!-- Thumbnail Tanda Bukti Transfer -->
            @if ($pinjaman->disbursement_receipt_path)
            <div class="form-group col-6 col-sm-2 justify-between">
                <a href="{{ asset($pinjaman->disbursement_receipt_path) }}" data-toggle="lightbox"
                    data-title="Tanda Bukti Transfer - {{$pinjaman->code}}" data-gallery="gallery">
                    <img src="{{ asset($pinjaman->disbursement_receipt_path) }}" class="img-fluid mb-2"
                        alt="white sample" />
                </a>
            </div>
            @endif
        </div>
        @if (in_array($pinjaman->status , ['pending','approved_by_chairman','disbursed_by_treasurer']))
        <!-- untuk ketua -->
        @if(Auth::user()->role->name == "Ketua" || Auth::user()->role->name == "Wakil Ketua")
        <!-- Ketua bisa mengkonfimrasi ketika status hanya peding -->
        @if ($pinjaman->status == "pending")
        <form action="{{ route('pinjaman.approved',Crypt::encrypt($pinjaman->id)) }}" method="POST"
            enctype="multipart/form-data" id="adminForm">
            @method('PATCH')
            {{csrf_field()}}
            <input type="hidden" name="status" value="approved_by_chairman">
            <input type="hidden" name="data_warga_id" value="{{Auth::user()->data_warga_id}}">

            <!-- Button Submit -->
            <button type="submit" class="btn btn-success" id="submitBtns">Setujui</button>
        </form>
        <!-- dan jika sudah mengkonfirmasi muncul muncul ini  -->
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
        @if ($pinjaman->status == "approved_by_chairman")
        <form action="{{ route('pinjaman.disbursed',Crypt::encrypt($pinjaman->id)) }}" method="POST"
            enctype="multipart/form-data" id="adminForm">
            @method('PATCH')
            {{csrf_field()}}
            <input type="hidden" name="amount" value="{{$pinjaman->loan_amount}}">
            <input type="hidden" name="status" value="disbursed_by_treasurer">

            <div class="form-group">
                <label for="disbursement_receipt_path">Upload Tanda Bukti <span class="text-danger">*</span></label>
                <input type="file" name="disbursement_receipt_path" id="disbursement_receipt_path"
                    value="{{old('disbursement_receipt_path')}}"
                    class="form-control col-12 @error('disbursement_receipt_path') is-invalid @enderror"
                    onchange="preview('.tampil-bukti', this.files[0])" required>

                <div class="tampil-bukti mt-3"></div>
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
        @elseif ($pinjaman->status == "pending")
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