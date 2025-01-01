@extends('user.layout.app')

@section('content')

<h2>Laporan Keuntungan</h2>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Keuntungan per Bulan dari {{Auth::user()->name}}</h3>
    </div>

    <!-- /.card-header -->
    <div class="card-body">
        <table class="table table-bordered table-striped datatable1 ">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Transaksi</th>
                    <th>Total Keuntungan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laporanBulan as $laporan)
                <tr>
                    <td>{{ Carbon\Carbon::createFromDate($laporan->tahun, $laporan->bulan, 1)->format('F Y') }}</td>
                    <td>{{ $laporan->total_transaksi }}</td>
                    <td>Rp {{ number_format($laporan->total_keuntungan, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection