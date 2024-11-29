@extends('user.layout.app')

@section('content')

@if ($bayarPinjaman->status == "process")
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Proses !</h5>
    Mohon menunggu konfirmasi dari pengurus. Jika ada pertanyaan, silakan hubungi pengurus melalui kontak resmi.
</div>
@endif
@if ($bayarPinjaman->status == "confirmed")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Data pembayaran Pinjaman sudah di konfirmasi, dan sudah masuk ke data.
</div>
@endif
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">{{$bayarPinjaman->data_warga->name}} ( {{$bayarPinjaman->code}} )</h3>
        <div class="card-tools">
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
                        <td>{{$bayarPinjaman->code}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>:</td>
                        <td>{{$bayarPinjaman->payment_date}}</td>
                    </tr>
                    <tr>
                        <td>Nama Anggota</td>
                        <td>:</td>
                        <td>{{$bayarPinjaman->data_warga->name}}</td>
                    </tr>
                    <tr>
                        <td>Di Input oleh</td>
                        <td>:</td>
                        <td>{{$bayarPinjaman->submitted->name}}</td>
                    </tr>
                    <tr>
                        <td>Nominal</td>
                        <td>:</td>
                        <td>Rp. {{number_format( $bayarPinjaman->amount, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Pembayaran</td>
                        <td>:</td>
                        <td>{{ $bayarPinjaman->payment_method}}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>
                            @if($bayarPinjaman->status === 'confirmed')
                            <span class="badge badge-success">Selesai</span>
                            @elseif($bayarPinjaman->status === 'process')
                            <span class="badge badge-warning">Menunggu persetujuan <br> Bendahara</span>
                            @elseif($bayarPinjaman->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                            @elseif($bayarPinjaman->status === 'pending')
                            <span class="badge badge-secondary">Pending</span>
                            @else
                            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                            @endif
                        </td>
                    </tr>
                    @if ($bayarPinjaman->status == "confirmed")
                    <tr>
                        <td>Di Konfirmasi Oleh</td>
                        <td>:</td>
                        <td>{{ $bayarPinjaman->confirmed->name}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal di Konfirmasi</td>
                        <td>:</td>
                        <td>{{ $bayarPinjaman->confirmation_date}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="card-footer">
                <p>
                    Keterangan : <br>
                    {{$bayarPinjaman->description}}
                </p>
            </div>
            <br>
            <!-- Thumbnail Tanda Bukti Transfer -->
            @if ($bayarPinjaman->payment_method == "transfer")
            <div class="form-group col-6 col-sm-2 justify-between">
                <a href="{{ asset($bayarPinjaman->transfer_receipt_path) }}" data-toggle="lightbox"
                    data-title="Tanda Bukti Transfer - {{$bayarPinjaman->code}}" data-gallery="gallery">
                    <img src="{{ asset($bayarPinjaman->transfer_receipt_path) }}" class="img-fluid mb-2"
                        alt="white sample" />
                </a>
            </div>
            @endif
        </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <p>
            Catatan : <br>
            - Data sudah di konfirmasi, cek kembali setiap data<br>
            - Data ini menjadi laporan <br>
        </p>
        <p><i> Satu Ikat kita Kuat</i></p>
    </div>
</div>
<!-- /.card -->


@endsection