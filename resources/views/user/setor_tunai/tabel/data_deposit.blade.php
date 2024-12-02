<!-- Kode ini untuk isi tabel di dalam index data_admin -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data semua Setor Tunai </h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable1 ">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Tanggal Input</th>
                    <th>Nama Warga</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($data_deposit as $data)
                <?php $no++; ?>
                <tr onclick="window.location='{{ route('setor-tunai.show',Crypt::encrypt($data->id)) }}'"
                    style="cursor: pointer;">
                    <td>{{$no}} </td>
                    <td>{{$data->code}} </td>
                    <td>{{$data->created_at}} </td>
                    <td>{{$data->submit->name}} </td>
                    <td>Rp {{number_format($data->amount,0,',','.')}} </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>