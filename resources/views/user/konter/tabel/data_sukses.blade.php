<!-- Kode ini untuk isi tabel di dalam index data_admin -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Transaksi Sukses </h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable1 ">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Type</th>
                    <th>status</th>
                    <th>Nama</th>
                    <th>Nominal</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($transaksi_selesai as $data)
                <?php $no++; ?>
                <tr onclick="window.location='{{ route('konter.show',Crypt::encrypt($data->id)) }}'"
                    style="cursor: pointer;">
                    <td>{{$no}} </td>
                    <td>{{$data->code}} </td>
                    <td>
                        @if($data->status === 'Selesai')
                        <span class="badge badge-success">Selesai</span>
                        @elseif($data->status === 'Berhasil')
                        <span class="badge badge-info">Berhasil</span>
                        @elseif($data->status === 'Proses')
                        <span class="badge badge-warning">Proses</span>
                        @elseif($data->status === 'Gagal')
                        <span class="badge badge-danger">Gagal</span>
                        @elseif($data->status === 'pending')
                        <span class="badge badge-secondary">Pending</span>
                        @else
                        <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                        @endif
                    </td>
                    <td>{{$data->product->kategori->name}} {{$data->product->provider->name}} </td>
                    <td>Rp {{number_format($data->product->amount,0,',','.')}} </td>
                    <td>Rp {{number_format($data->price,0,',','.')}} </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>