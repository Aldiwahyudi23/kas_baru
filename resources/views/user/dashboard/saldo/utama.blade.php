@extends('user.layout.app')

@section('content')
<!-- Info boxes -->

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Perhitungan SALDO</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table class="table table-bordered table-striped datatable table-auto w-full text-center">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Tanggal</th>
                    <th rowspan="2">Kode</th>
                    <!-- <th rowspan="2">Alokasi</th> -->
                    <th colspan="2">Nominal</th>
                    <th colspan="2">Saldo</th>
                    <th rowspan="2">Total</th>
                </tr>
                <tr>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Cash</th>
                    <th>Bank</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($saldoData as $data)
                <?php $no++; ?>
                <tr class="{{ $data->amount < 0 ? 'negative-row' : 'positive-row' }}">
                    <td>{{$no}} </td>
                    <td>{{$data->created_at}} </td>
                    <td>{{$data->code}} </td>
                    <!-- <td>{{$data->code}} </td> -->
                    <td>
                        @if ($data->amount >= 0)
                        <span>Rp {{ number_format($data->amount, 2, ',', '.') }}</span>
                        @else
                        -
                        @endif
                    </td>
                    <td>
                        @if ($data->amount < 0) <span>Rp {{ number_format($data->amount, 2, ',', '.') }}</span>
                            @else
                            -
                            @endif
                    </td>

                    <td>Rp {{ number_format($data->cash_outside, 2, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->atm_balance, 2, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->total_balance, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>


    </div>
    <div class="card-footer">
        <p>
            Keterangan : <br>
            - Perhitungan SALDO ini berfungsi mencatat semua dana yang masuk dan keluar.<br>
            - kode yang tercantum menjelaskan bahwa teransaksi tersebut dengan kode tersebut. <br>
            - Data disusun dari yang terbaru ke yang terlama. <br>
            - Warna Hijau artinya Dana Masuk. <br>
            - Warna Merah artinya Dana Keluar. <br>
        </p>
        <p>
            Tujuan : <br>
            - Semua pemasukan dan pengeluaran tercatat diSini, yang bertujuan untuk Transparan dalam pengeloaan.
            - Memudahkan mengecek Saldo
        </p>
    </div>
    <!-- /.card-body -->
</div>

<style>
    /* Default (light mode) */
    .positive-row {
        background-color: #d4edda !important;
        /* Hijau muda */
        color: #155724 !important;
        /* Hijau teks */
    }

    .negative-row {
        background-color: #f8d7da !important;
        /* Merah muda */
        color: #721c24 !important;
        /* Merah teks */
    }

    /* Dark mode compatibility */
    .dark-mode .positive-row {
        background-color: #2e7d32 !important;
        /* Hijau gelap */
        color: #e8f5e9 !important;
        /* Hijau terang teks */
    }

    .dark-mode .negative-row {
        background-color: #c62828 !important;
        /* Merah gelap */
        color: #ffebee !important;
        /* Merah terang teks */
    }
</style>

<style>
    .positive-row,
    .negative-row {
        background-color: rgba(40, 167, 69, 0.15) !important;
        /* Transparansi hijau */
    }

    .dark-mode .positive-row {
        background-color: rgba(46, 125, 50, 0.2) !important;
        /* Transparansi hijau gelap */
    }

    .dark-mode .negative-row {
        background-color: rgba(198, 40, 40, 0.2) !important;
        /* Transparansi merah gelap */
    }
</style>


@endsection