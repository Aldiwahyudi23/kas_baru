@extends('user.layout.app')

@section('content')
<!-- Info boxes -->

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Informasi Saldo Kas dan Bank</h3>
    </div>

    <!-- /.card-header -->
    <div class="card-body">
        <!-- Summary Information -->
        <div class="alert alert-info">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-wallet"></i> Saldo Aktual di Rekening Kas</h5>
                    <h3 class="text-success">Rp {{ number_format($totalActualBalance, 2, ',', '.') }}</h3>
                    <small class="text-muted">Total saldo aktual semua rekening bank</small>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-calculator"></i> Saldo Terhitung Sistem</h5>
                    <h3 class="{{ $difference >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($systemBalance, 2, ',', '.') }}</h3>
                    <small class="text-muted">Saldo bank berdasarkan sistem</small>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-balance-scale"></i> Selisih</h5>
                    <h3 class="{{ $difference == 0 ? 'text-primary' : ($difference > 0 ? 'text-success' : 'text-danger') }}">
                        Rp {{ number_format(abs($difference), 2, ',', '.') }}
                        @if($difference != 0)
                        <small>({{ $difference > 0 ? 'Lebih' : 'Kurang' }})</small>
                        @endif
                    </h3>
                    <small class="text-muted">Perbedaan antara aktual dan sistem</small>
                </div>
            </div>
        </div>

        <!-- Section untuk menampilkan saldo bank -->
        <div class="card mb-4">
            <div class="card-header bg-primary">
                <h3 class="card-title text-white">Saldo Rekening Bank</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($bankBalances as $balance)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <!-- Nama Bank dan Saldo -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title font-weight-bold mb-0 text-truncate" style="max-width: 60%">
                                        {{ $balance->bankAccount->bank_name }}
                                    </h5>
                                    <h5 class="{{ $balance->balance >= 0 ? 'text-success' : 'text-danger' }} font-weight-bold mb-0">
                                        Rp {{ number_format($balance->balance, 2, ',', '.') }}
                                    </h5>
                                </div>

                                <!-- Info Rekening dan Pemilik -->
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-credit-card mr-2 text-muted" style="width: 20px"></i>
                                        <small class="text-muted">{{ $balance->bankAccount->account_number }}</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user mr-2 text-muted" style="width: 20px"></i>
                                        <small class="text-muted">{{ $balance->bankAccount->warga->name ?? '-' }}</small>
                                    </div>
                                </div>
                                <!-- Update dan Tombol -->
                                <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                    <small class="text-muted">
                                        Update: {{ \Carbon\Carbon::parse($balance->created_at)->format('d M Y') }}
                                    </small>
                                    @if ($balance->bankAccount->warga->id == auth()->user()->data_warga_id)
                                        <a href="{{ route('bank.transfer.form', ['bankAccount' => $balance->bank_account_id]) }}" 
                                    class="btn btn-sm btn-outline-primary"
                                    title="Transfer Dana">
                                        <i class="fas fa-exchange-alt"></i> Transfer Dana
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Section untuk perhitungan saldo -->
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title text-white">Perhitungan Saldo</h3>
            </div>
            <div class="card-body">
                <!-- Filter dan Tabel -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select id="filter-bulan" class="form-control">
                            <option value="">Pilih Bulan</option>
                            <option value="January">January</option>
                            <option value="February">February</option>
                            <option value="March">March</option>
                            <option value="April">April</option>
                            <option value="May">May</option>
                            <option value="June">June</option>
                            <option value="July">July</option>
                            <option value="August">August</option>
                            <option value="September">September</option>
                            <option value="October">October</option>
                            <option value="November">November</option>
                            <option value="December">December</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filter-tahun" class="form-control">
                            <option value="">Pilih Tahun</option>
                            @php
                            $currentYear = date('Y');
                            for ($year = $currentYear - 10; $year <= $currentYear + 10; $year++) {
                                echo "<option value='$year'>$year</option>" ; } @endphp </select>
                    </div>
                </div>

                <table class="table table-bordered table-striped datatable table-auto w-full text-center">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Tanggal</th>
                            <th rowspan="2">Kode</th>
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
                            <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d-F-Y H:i:s') }}</td>
                            <td>{{$data->code}} </td>
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
        </div>
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
    background-color: rgba(40, 167, 69, 0.15) !important;
    color: #155724 !important;
}
.negative-row {
    background-color: rgba(220, 53, 69, 0.15) !important;
    color: #721c24 !important;
}
.dark-mode .positive-row {
    background-color: rgba(46, 125, 50, 0.2) !important;
    color: #e8f5e9 !important;
}
.dark-mode .negative-row {
    background-color: rgba(198, 40, 40, 0.2) !important;
    color: #ffebee !important;
}
</style>
@endsection