@extends('user.layout.app')

@section('content')

@if ($pengeluaran->status != "Acknowledged")
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Proses !</h5>
    Anggaran masih dalam proses oleh pengurus
</div>
@endif
@if ($pengeluaran->status == "Acknowledged")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Data Anggaran sudah selesai, di Konfirmasi dan telah di keluarkan sesuai data di bawah
</div>
@endif
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">{{$pengeluaran->sekretaris->name}} ( {{$pengeluaran->code}} )</h3>
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

                        <tr>
                            <td>Di Konfirmasi Oleh</td>
                            <td>:</td>
                            <td>{{ isset($pengeluaran->ketua->name) ? $pengeluaran->ketua->name : 'Proses...' }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal di Konfirmasi</td>
                            <td>:</td>
                            <td>{{ isset($pengeluaran->approved_date) ? $pengeluaran->approved_date : 'Proses...'}}</td>
                        </tr>
                        <tr>
                            <td>Pencaian</td>
                            <td>:</td>
                            <td>{{ isset($pengeluaran->bendahara->name) ? $pengeluaran->bendahara->name : 'Proses...'}}</td>
                        </tr>
                        <tr>
                            <td>Tanggal di cairkan</td>
                            <td>:</td>
                            <td>{{ isset($pengeluaran->disbursed_date) ? $pengeluaran->disbursed_date : 'Proses...'}}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="card-footer">
                    <p>
                        Keterangan : <br>
                        {!!$pengeluaran->description!!}
                    </p>
                </div>
                <br>

                <div class="form-group col-6 col-sm-2 justify-between">
                    <a href="{{ asset('storage/'.$pengeluaran->receipt_path) }}" data-toggle="lightbox"
                        data-title="Tanda Bukti Transfer - {{$pengeluaran->code}}" data-gallery="gallery">
                        <img src="{{ asset('storage/'.$pengeluaran->receipt_path) }}" class="img-fluid mb-2"
                            alt="white sample" />
                    </a>
                </div>
        </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <p>
            Catatan : <br>
            - Data sudah di konfirmasi, cek kembali setiap data<br>
            - Data sudah di keluarkan<br>
            - Data ini menjadi laporan yang sangat penting<br>
        </p>
        <p><i> Semoga bermanfaat untuk kita semua</i></p>
    </div>
</div>
<!-- /.card -->


@endsection