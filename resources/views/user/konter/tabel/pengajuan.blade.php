
        <table  class="table table-responsive datatable1 ">
            <thead>
                <tr>
                    <th>status</th>
                    <th>Kategori</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($pengajuan_proses as $data)
                <?php $no++; ?>
                <tr onclick="window.location='{{ route('konter.pengajuan',Crypt::encrypt($data->id)) }}'"
                    style="cursor: pointer;">
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

                </tr>
                @endforeach
            </tbody>
        </table>