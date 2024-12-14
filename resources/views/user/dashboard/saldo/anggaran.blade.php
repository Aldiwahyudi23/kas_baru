@extends('user.layout.app')

@section('content')
<!-- Info boxes -->

<!-- Kode ini untuk isi tabel di dalam index data_admin -->

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Perhitungan Saldo Anggaran : {{ $type }}</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Anggaran</th>
                    <th>Persentase</th>
                    <th>Nominal</th>
                    <th>Total Saldo Anggaran</th>
                    <th>Dibuat</th>
                    <th>Diupdate</th>

                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($saldoAnggaran as $data)
                <?php $no++; ?>
                <tr>
                    <td>{{$no}} </td>
                    <td>{{$data->saldos->code}} </td>
                    <td>{{$data->type}} </td>
                    <td>{{$data->percentage}} </td>
                    <td>Rp {{number_format($data->amount, 2, ',','.')}} </td>
                    <td>Rp {{number_format($data->saldo, 2, ',','.')}} </td>
                    <td>{{$data->created_at}} </td>
                    <td>{{$data->updated_at}} </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <p>
            Keterangan : <br>
            - Perhitungan SALDO ini berfungsi mencatat semua dana yang masuk dan keluar.<br>
            - kode yang tercantuk menjelaskan bahwa teransaksi tersebut dengan berkode tersebut. <br>
            - Data disusun dari yang terbaru ke yang terlama. <br>
        </p>
        <p>
            Tujuan : <br>
            - Semua pemasukan dan pengeluaran tercatat diSini, yang bertujuan untuk Tranparan dalam pengeloaan.
            - Memudahkan mengecek Saldo
        </p>
    </div>
    <!-- /.card-body -->
</div>
@endsection