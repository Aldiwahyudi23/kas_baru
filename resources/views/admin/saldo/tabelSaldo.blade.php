<!-- Kode ini untuk isi tabel di dalam index data_admin -->

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Perhitungan Saldo</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nominal</th>
                    <th>Saldo Akhir</th>
                    <th>Uang di Luar belum TF</th>
                    <th>Saldo Bank</th>
                    <th>Total Keseluruhan</th>
                    <th>Dibuat</th>
                    <th>Diupdate</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($saldo as $data)
                <?php $no++; ?>
                <tr>
                    <td>{{$no}} </td>
                    <td>{{$data->code}} </td>
                    <td>Rp {{ number_format($data->amount, 2, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->ending_balance, 2, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->cash_outside, 2, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->atm_balance, 2, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->total_balance, 2, ',', '.') }}</td>
                    <td>{{$data->created_at}} </td>
                    <td>{{$data->updated_at}} </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>

@section('script')

@endsection