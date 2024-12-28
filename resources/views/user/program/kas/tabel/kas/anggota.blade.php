<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Semua Pemasukan Anggota</h3>
        <form method="GET" action="{{ route('kas.index') }}" class="float-right">
            <label for="filter_tahun">Filter Tahun:</label>
            <select name="filter_tahun" id="filter_tahun" class="form-control"
                style="width: auto; display: inline-block;" onchange="this.form.submit()">
                @foreach($available_years as $year)
                <option value="{{ $year }}" {{ request('filter_tahun') == $year ? 'selected' : '' }}>
                    {{ $year }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped datatable1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Bulan</th>
                    <th>Kode</th>
                    <th>Tanggal Input</th>
                    <th>Nama Warga</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($monthlyData as $month => $data)
                @if($data['payments']->isNotEmpty())
                @foreach($data['payments'] as $payment)
                <?php $no++; ?>
                <tr onclick="window.location='{{ route('kas.show',Crypt::encrypt($payment->id)) }}'"
                    style="cursor: pointer;">
                    <td>{{ $no }}</td>
                    <td>{{ $data['month_name'] }}</td>
                    <td>{{ $payment->code }}</td>
                    <td>{{ $payment->created_at->format('d-m-Y') }}</td>
                    <td>{{ $payment->data_warga->name }}</td>
                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td>{{ ++$no }}</td>
                    <td>{{ $data['month_name'] }}</td>
                    <td colspan="1" class="text-center">Tidak ada pembayaran untuk bulan ini.</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>