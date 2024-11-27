<!-- Kode ini untuk isi tabel di dalam index data_admin -->

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Warga</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Account apk</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php

                use App\Models\User;

                $no = 0; ?>
                @foreach($warga as $data)
                <?php $no++;
                $cek_user = User::where('data_warga_id', $data)->count();
                if ($cek_user == 1) {
                    $status_user = "Aktif";
                } else {
                    $status_user = "Belum Aktif";
                }
                ?>
                <tr>
                    <td>{{$no}} </td>
                    <td>{{$data->code}} </td>
                    <td>{{$data->name}} </td>
                    <td>{{$data->jenis_kelamin}} </td>
                    <td>
                        <button type="button" class="btn {{ $cek_user && $cek_user ? 'btn-success' : 'btn-danger' }}"
                            data-url="{{ route('anggaran.toggleStatus', Crypt::encrypt($data->id)) }}"
                            data-active="{{ $cek_user && $cek_user ? 1 : 0 }}"
                            onclick="toggleAccess(this)">
                            {{ $cek_user && $cek_user ? 'ON' : 'OFF' }}
                        </button>
                    </td>
                    <td class="project-actions text-right">
                        <a class="btn btn-primary btn-sm" href="{{route('warga.show',Crypt::encrypt($data->id))}}">
                            <i class="fas fa-folder">
                            </i>
                            View
                        </a>
                        <a class="btn btn-danger btn-sm" href="{{route('warga.destroy',Crypt::encrypt($data->id))}}" class="btn btn-danger" data-confirm-delete="true">
                            <i class="fas fa-trash">
                            </i>
                            Delete
                        </a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>

@section('script')

@endsection