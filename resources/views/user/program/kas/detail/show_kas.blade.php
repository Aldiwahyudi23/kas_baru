@extends('user.layout.app')

@section('content')

@if ($kas_payment->status == "process")
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Proses !</h5>
    Mohon menunggu konfirmasi dari pengurus. Jika ada pertanyaan, silakan hubungi pengurus melalui kontak resmi.
</div>
@endif
@if ($kas_payment->status == "confirmed")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Data pembayaran kas sudah di konfirmasi, dan sudah masuk ke data.
</div>
@endif
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">{{$kas_payment->data_warga->name}} ( {{$kas_payment->code}} )</h3>
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
                        <td>{{$kas_payment->code}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>:</td>
                        <td>{{$kas_payment->payment_date}}</td>
                    </tr>
                    <tr>
                        <td>Nama Anggota</td>
                        <td>:</td>
                        <td>{{$kas_payment->data_warga->name}}</td>
                    </tr>
                    <tr>
                        <td>Di Input oleh</td>
                        <td>:</td>
                        <td>{{$kas_payment->submitted->name}}</td>
                    </tr>
                    <tr>
                        <td>Nominal</td>
                        <td>:</td>
                        <td>Rp. {{number_format( $kas_payment->amount, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Pembayaran</td>
                        <td>:</td>
                        <td>{{ $kas_payment->payment_method}} <br>
                            @if ($kas_payment->is_deposited == true)
                            <span class="badge badge-success"><i class="icon fas fa-check"></i> Done Setor</span>
                            @elseif($kas_payment->is_deposited == false)
                            <span class="badge badge-warning"><i class="icon fas fa-exclamation-triangle"></i> Belum di
                                setor</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>
                            @if($kas_payment->status === 'confirmed')
                            <span class="badge badge-success">Selesai</span>
                            @elseif($kas_payment->status === 'process')
                            <span class="badge badge-warning">Menunggu persetujuan <br> Bendahara</span>
                            @elseif($kas_payment->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                            @elseif($kas_payment->status === 'pending')
                            <span class="badge badge-secondary">Pending</span>
                            @else
                            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                            @endif
                        </td>
                    </tr>
                    @if ($kas_payment->status == "confirmed")
                    <tr>
                        <td>Di Konfirmasi Oleh</td>
                        <td>:</td>
                        <td>{{ $kas_payment->confirmed->name}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal di Konfirmasi</td>
                        <td>:</td>
                        <td>{{ $kas_payment->confirmation_date}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="card-footer">
                <p>
                    Keterangan : <br>
                    {{$kas_payment->description}}
                </p>
            </div>
            <br>
            <!-- Thumbnail Tanda Bukti Transfer -->
            @if ($kas_payment->payment_method == "transfer")
            <div class="form-group col-6 col-sm-2 justify-between">
                <a href="{{ asset($kas_payment->transfer_receipt_path) }}" data-toggle="lightbox"
                    data-title="Tanda Bukti Transfer - {{$kas_payment->code}}" data-gallery="gallery">
                    <img src="{{ asset($kas_payment->transfer_receipt_path) }}" class="img-fluid mb-2"
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