@extends('user.layout.app')

@section('content')

@if ($pinjaman->status == ['pending', 'approved_by_chairman', 'disbursed_by_treasurer'])
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Proses !</h5>
    Pengajuan Pinjaman masih dalam Proses, harap bersabar dan slalu cek status pengajuan di bawah.
</div>
@endif
@if ($pinjaman->status == ['Acknowledged','In Repayment', 'Paid in Full'])
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i>Sudah di terima </h5>
    Pinjaman sudah di setujui dan sudah di terima.
</div>
@endif
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">{{$pinjaman->sekretaris->name}} ( {{$pinjaman->code}} )</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <table class="table table-hover text-nowrap">
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
                            <td>Nama Warga</td>
                            <td>:</td>
                            <td>{{$pinjaman->warga->name}}</td>
                        </tr>
                        <tr>
                            <td>Di Input oleh</td>
                            <td>:</td>
                            <td>{{$pinjaman->sekretaris->name}}</td>
                        </tr>
                        <tr>
                            <td>Nominal</td>
                            <td>:</td>
                            <td>Rp. {{number_format( $pinjaman->loan_amount, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Sisa</td>
                            <td>:</td>
                            <td>Rp. {{number_format( $pinjaman->remaining_balance, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:</td>
                            <td>
                                @if($pinjaman->status === 'Acknowledged')
                                <span class="badge badge-success">Selesai</span>
                                @elseif($pinjaman->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                                @elseif($pinjaman->status === 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                                @elseif($pinjaman->status === 'approved_by_chairman')
                                <span class="badge badge-secondary">Menunggu persetujuan Ketua</span>
                                @elseif($pinjaman->status === 'disbursed_by_treasurer')
                                <span class="badge badge-secondary">Dalam Proses Pencairan</span>
                                @else
                                <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                                @endif
                            </td>
                        </tr>

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
                    </tbody>
                </table>
                <div class="card-footer">
                    <p>
                        Keterangan : <br>
                        {!!$pinjaman->description!!}
                    </p>
                </div>
                <br>

                <div class="form-group col-6 col-sm-2 justify-between">
                    <a href="{{ asset('storage/'.$pinjaman->disbursement_receipt_path) }}" data-toggle="lightbox"
                        data-title="Tanda Bukti Transfer - {{$pinjaman->code}}" data-gallery="gallery">
                        <img src="{{ asset('storage/'.$pinjaman->disbursement_receipt_path) }}" class="img-fluid mb-2"
                            alt="white sample" />
                    </a>
                </div>
        </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <p>
            Catatan : <br>
            - Selalu bayar tepat waktu, dan di usahakan setiap bulannya bayar (cicil)<br>
            - Setiap Pengajuan telah di atur<br>
        </p>
        <p><i> Semoga Amanah, dan Kerjasamanya</i></p>
    </div>
</div>
<!-- /.card -->


@endsection