<!-- Kode ini untuk isi tabel di dalam index data_admin -->

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Perhitungan Saldo Anggaran</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Saldo Kas</th>
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
                @foreach($anggaran_saldo as $data)
                <?php $no++; ?>
                <tr>
                    <td>{{$no}} </td>
                    <td>{{$data->saldo_id}} </td>
                    <td>Rp {{number_format($data->cash_saldo, 2, ',','.')}} </td>
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
    <!-- /.card-body -->
</div>

@section('script')

@endsection