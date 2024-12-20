@extends('user.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pinjaman Yang masih Proses</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped datatable">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Status</th>
                            <th>Kode</th>
                            <th>Sisa Waktu</th>
                            <th>Jatuh Tempo</th>
                            <th>Nama Warga</th>
                            <th>Nominal</th>
                            <th>Sisa</th>
                            <th>Lebih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        use Carbon\Carbon;

                        $no = 0; ?>
                        @foreach($pinjaman_proses as $data)
                        <?php $no++;
                        // // Tanggal pembuatan pinjaman terbaru
                        // // $loanCreationDate = Carbon::parse($data->created_at);
                        // $loanCreationDate = Carbon::now();
                        // // Tanggal pembayaran terakhir (cek apakah $lastRepayment ada)
                        // $lastPaymentDate = Carbon::parse($data->deadline_date);
                        // // $lastPaymentDate = Carbon::now();
                        // // Cek jika selisih antara pembayaran terakhir dan pengajuan baru kurang dari sebulan
                        // $waktubayar = $loanCreationDate->diffInDays($lastPaymentDate);
                        // $waktuPembayaran =  round($waktubayar); 
                        ?>
                        <tr>
                            <td>{{$no}} </td>
                            <td>
                                @if($data->status === 'Paid in Full')
                                <a href="{{ route('pinjaman.show',Crypt::encrypt($data->id)) }}">
                                    <span class="btn btn-success">Lunas</span>
                                </a>
                                @elseif($data->status === 'pending')
                                <span class="btn btn-warning">Proses</span>
                                @elseif($data->status = ['Acknowledged', 'In Repayment'])
                                <a href="{{ route('bayar-pinjaman.pembayaran',Crypt::encrypt($data->id)) }}">
                                    <span class="btn btn-danger">Bayar</span>
                                </a>
                                @elseif($data->status === 'approved_by_chairman')
                                <span class="btn btn-secondary">Disetujui</span>
                                @elseif($data->status === 'disbursed_by_treasurer')
                                <span class="btn btn-secondary">Pencairan</span>
                                @else
                                <span class="btn btn-light">Unknown</span> <!-- default if status is undefined -->
                                @endif
                            </td>
                            <td>{{$data->code}} </td>
                            <td>
                                <div class="alert 
                                @if($data->remaining_time <= 3 ) alert-danger @elseif($data->remaining_time <=14)
                                        alert-warning @else alert-success @endif alert-dismissible">
                                    <center>
                                        @if($data->remaining_time == 0) Jatuh Tempo
                                        @elseif($data->remaining_time <= -1) Lewat {{ $data->remaining_time }} hari
                                            segera bayar @else {{ $data->remaining_time }} hari Lagi @endif </center>
                                </div>
                            </td>
                            <td>{{$data->deadline_date}} </td>
                            <td>{{$data->warga->name}} </td>
                            <td>Rp {{number_format($data->loan_amount, 2,',','.')}} </td>
                            <td>Rp {{number_format($data->remaining_balance, 2,',','.')}} </td>
                            <td>Rp {{number_format($data->overpayment_balance, 2,',','.')}} </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
</div>
<!-- /.row -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pinjaman yang sudah Selesai</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped datatable">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Status</th>
                            <th>Kode</th>
                            <th>Tanggal Input</th>
                            <th>Nama Warga</th>
                            <th>Nominal</th>
                            <th>Sisa</th>
                            <th>Lebih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 0; ?>
                        @foreach($pinjaman_selesai as $data)
                        <?php $no++; ?>
                        <tr>
                            <td>{{$no}} </td>
                            <td>
                                @if($data->status === 'Paid in Full')
                                <a href="{{ route('pinjaman.show',Crypt::encrypt($data->id)) }}">
                                    <span class="btn btn-success">Lunas</span>
                                </a>
                                @elseif($data->status === 'pending')
                                <span class="btn btn-warning">Proses</span>
                                @elseif($data->status = ['Acknowledged', 'In Repayment'])
                                <a href="{{ route('bayar-pinjaman.pembayaran',Crypt::encrypt($data->id)) }}">
                                    <span class="btn btn-danger">Bayar</span>
                                </a>
                                @elseif($data->status === 'approved_by_chairman')
                                <span class="btn btn-secondary">Disetujui</span>
                                @elseif($data->status === 'disbursed_by_treasurer')
                                <span class="btn btn-secondary">Pencairan</span>
                                @else
                                <span class="btn btn-light">Unknown</span> <!-- default if status is undefined -->
                                @endif
                            </td>
                            <td>{{$data->code}} </td>
                            <td>{{$data->created_at}} </td>
                            <td>{{$data->warga->name}} </td>
                            <td>Rp {{number_format($data->loan_amount, 2,',','.')}} </td>
                            <td>Rp {{number_format($data->remaining_balance, 2,',','.')}} </td>
                            <td>Rp {{number_format($data->overpayment_balance, 2,',','.')}} </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
</div>
@endsection

@section('script')


@endsection