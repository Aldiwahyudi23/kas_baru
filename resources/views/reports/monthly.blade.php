<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 20px;
        line-height: 1.5;
    }

    h2 {
        font-size: 12px;
        font-weight: bold;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        padding: 4px 8px;
        text-align: left;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f4f4f4;
        text-align: center;
    }

    .positive-row {
        background-color: #d4edda;
        color: #155724;
    }

    .negative-row {
        background-color: #f8d7da;
        color: rgb(14, 13, 13);
    }
    </style>


    <style>
    .table-description {
        font-size: 12px;
        color: #555;
        margin-bottom: 10px;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        margin-bottom: 20px;
    }

    .custom-table td {
        padding: 4px 8px;
        border-bottom: 1px solid #ddd;
        text-align: left;
        border: none;
        /* Menghapus border antar kolom */
        line-height: 1.2;
        /* Mengurangi tinggi baris */
    }

    .custom-table td:nth-child(2) {
        text-align: right;
        font-weight: bold;

    }
    </style>

    <style>
    .page-break {
        page-break-before: always;
    }

    @page {
        margin: 100px 50px 100px 50px;
        /* Top, Right, Bottom, Left */
    }

    /* Header */
    header {
        position: fixed;
        top: -80px;
        left: 0;
        right: 0;
        height: 50px;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        border-bottom: 1px solid #ddd;
        padding: 10px 0;
    }

    /* Footer */
    footer {
        position: fixed;
        bottom: -80px;
        left: 0;
        right: 0;
        height: 50px;
        text-align: center;
        font-size: 12px;
        border-top: 1px solid #ddd;
        padding: 10px 0;
    }

    /* Page Numbering */
    .page-number:after {
        content: counter(page);
    }
    </style>

</head>

<body>
    <?php

    use App\Models\CompanyInformation;
    use App\Models\Konter\DetailTransaksiKonter;
    use Carbon\Carbon;

    // Fetching company information
    $companyInfo = CompanyInformation::first();
    ?>

    <!-- Header -->
    <header>
        Laporan Keuangan Bulanan || Kas Keluarga Ma Haya <br>
        {{ strtolower(Carbon::now()->format('F Y')) }}

    </header>

    <!-- Footer -->
    <footer>
        Halaman <span class="page-number"></span>
    </footer>

    <!-- Main Content -->
    <div class="container">
        <!-- Kas User Section -->
        <div class="section">
            <h2>Laporan Transaksi {{$name}}</h2>
            <div class="left">Pembayaran KAS</div>
            @if($kasPayments->isNotEmpty())
            <table class="custom-table">
                <tbody>
                    @foreach ($kasPayments as $payment)
                    <tr>
                        <td>{{$payment->created_at}}</td>
                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <table class="custom-table">
                <tr>
                    <td colspan="2">Tidak ada pembayaran Kas bulan ini.</td>
                </tr>
            </table>
            @endif

        </div>

        <!-- Tagihan Section -->
        <div class="section">
            <div class="left">Tagihan Konter atas nama</div>
            @if($konters->isNotEmpty())
            <table class="custom-table">
                <tbody>
                    @foreach ($konters as $data)
                    <tr>
                        <td>{{$data->product->kategori->name}} {{$data->product->provider->name}}
                            {{$data->detail->name}}
                        </td>
                        <td>Rp {{ number_format($data->invoice, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <table class="custom-table">
                <tr>
                    <td colspan="2">Tidak ada tagihan Konter bulan ini.</td>
                </tr>
            </table>
            @endif


            <div class="left">Pinjaman Aktif atas nama</div>
            @if($loans->isNotEmpty())
            <table class="custom-table">
                <tbody>
                    @foreach ($loans as $data)
                    <tr>
                        <td>{{$data->warga->name}} Pinjaman Rp {{ number_format($data->loan_amount, 0, ',', '.') }}
                        </td>
                        <td>Tagihan/Sisa Rp {{ number_format($data->remaining_balance, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <table class="custom-table">
                <tr>
                    <td colspan="3">Tidak ada tagihan Pinjaman bulan ini.</td>
                </tr>
            </table>
            @endif
        </div>


        <!-- Saldo Bulan Ini Section -->
        <div class="section">
            <div class="left">Transaksi Bulan ini</div>
            <table class="custom-table">
                <tbody>

                    <tr>
                        <td>Pemasukan Kas</td>
                        <td>Rp {{ number_format($kasPaymentsTotal, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Pemasukan Lain</td>
                        <td>Rp {{ number_format($otherIncomesTotal, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Penghasilan Konter</td>
                        <td>Rp {{ number_format($totalKonter, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Lebih Pinjaman</td>
                        <td>Rp {{ number_format($totalOverPaymentBalance, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Uang yang Masih di pinjam</td>
                        <td>Rp {{ number_format($totalRemainingBalance, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Uang yang dipake Konter (Bayar Nanti)</td>
                        <td>Rp {{ number_format($konterAktif, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Pengeluaran KAS</td>
                        <td>Rp {{ number_format($cashExpendituresTotal, 2, ',', '.') }}</td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- Saldo Bulan Ini Section -->
        <div class="section">
            <div class="left">Informasi Saldo</div>
            <table class="custom-table">
                <tbody>
                    <tr>
                        <td>Saldo Bulan Lalu</td>
                        <td>Rp {{ number_format($saldo_bulan_lalu->total_balance, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td> &nbsp;&nbsp; Saldo Kas</td>
                        <td>Rp {{ number_format($saldoKas->saldo, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td> &nbsp;&nbsp; Saldo Pinjaman yang tersisa</td>
                        <td>Rp {{ number_format($saldoPinjam->saldo, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td> &nbsp;&nbsp; Saldo Darurat</td>
                        <td>Rp {{ number_format($saldoDarurat->saldo, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td> &nbsp;&nbsp; Saldo Amal</td>
                        <td>Rp {{ number_format($saldoAmal->saldo, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Total Tercatat (Belum termasuk nominal yang masih di Pinjam)</td>
                        <td>Rp {{ number_format($saldoTotal->total_balance, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Total Saldo keseluruhan (Total Tercatat + Uang yang Masih di pinjam + Konter (Bayar Nanti) )
                        </td>
                        <td>Rp
                            {{ number_format($saldoTotal->total_balance + $totalRemainingBalance + $konterAktif, 2, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="table-description">
                Keterangan: <br>
                - <b>Saldo Tercatat</b> adalah saldo yang telah dihitung dan dicatat berdasarkan jumlah aktual yang
                tersedia di bank. Saldo ini tidak termasuk dana yang masih dipinjam. <br>
                - Untuk mengetahui total saldo keseluruhan, jumlahkan <b>Saldo Tercatat</b> dengan <b>Uang yang Masih
                    Dipinjam</b> dan <b>Konter Bayar Nanti</b>.

            </p>
        </div>
        <div class="page-break"></div>
        <!-- Transaksi Kas Table -->
        <h2>Kas</h2>
        <p class="table-description">
            Tabel ini merupakan laporan hasil transaksi Pemasukan KAS selama satu bulan terakhir...
        </p>
        <table class="table">
            bl <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nominal</th>
                    <th>Dibuat</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($data_kas as $data)
                <?php $no++; ?>
                <tr>
                    <td>{{ $no }}</td>
                    <td>{{ $data->code }}</td>
                    <td>Rp {{ number_format($data->amount, 2, ',', '.') }}</td>
                    <td>{{ $data->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="page-break"></div>

        <!-- Transaksi Pinjaman Table -->
        <h2>Pinjaman</h2>
        <p class="table-description">
            Tabel ini merupakan laporan hasil transaksi pinjaman selama satu bulan terakhir...
        </p>
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nominal</th>
                    <th>Sisa</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($Data_loan as $data)
                <?php $no++; ?>
                <tr>
                    <td>{{ $no }}</td>
                    <td>{{ $data->code }}</td>
                    <td>Rp {{ number_format($data->loan_amount, 2, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->remaining_balance, 2, ',', '.') }}</td>
                    <td>
                        @if($data->status === 'Acknowledged')
                        <span class="badge badge-success">Uang sudah diterima</span>
                        @elseif($data->status === 'pending')
                        <span class="badge badge-warning">Menunggu persetujuan Ketua</span>
                        @elseif($data->status === 'rejected')
                        <span class="badge badge-danger">Rejected</span>
                        @elseif($data->status === 'approved_by_chairman')
                        <span class="badge badge-secondary">Proses Pencairan</span>
                        @elseif($data->status === 'In Repayment')
                        <span class="badge badge-success">Proses Cicil</span>
                        @elseif($data->status === 'Paid in Full')
                        <span class="badge badge-success">Selesai / Lunas</span>
                        @elseif($data->status === 'disbursed_by_treasurer')
                        <span class="badge badge-secondary">Sudah di cairkan, <br> Menunggu konfirmasi bahwa uang telah
                            di terima </span>
                        @else
                        <span class="badge badge-light">Unknown</span>
                        @endif
                    </td>
                    <td>{{ $data->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="page-break"></div>

        <!-- Transaksi Mutasi Table -->
        <h2>Mutasi</h2>
        <p class="table-description">
            Tabel ini mencakup data pemasukan dan pengeluaran kas keluarga selama satu bulan terakhir.
            Data ini memberikan gambaran arus keuangan keluarga.
        </p>

        <table class="table table-auto w-full text-center">
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
                @foreach($data_saldo as $data)
                <?php $no++; ?>
                <tr class="{{ $data->amount < 0 ? 'negative-row' : 'positive-row' }}">
                    <td>{{ $no }}</td>
                    <td>{{ $data->created_at }}</td>
                    <td>{{ $data->code }}</td>
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
</body>

</html>