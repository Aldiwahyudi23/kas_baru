<!-- Kode ini untuk isi tabel di dalam index data_admin -->

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Role</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Role</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($role as $data)
                <?php $no++; ?>
                <tr>
                    <td>{{$no}} </td>
                    <td>{{$data->code}} </td>
                    <td>{{$data->name}} </td>
                    <td>
                        <form class="statusrole" method="POST">
                            {{ csrf_field() }}
                            <button type="button" class="btn {{ $data && $data->is_active ? 'btn-success' : 'btn-danger' }}"
                                data-url="{{ route('role.toggleStatus', Crypt::encrypt($data->id)) }}"
                                data-active="{{ $data && $data->is_active ? 1 : 0 }}"
                                onclick="toggleAccess(this)">
                                {{ $data && $data->is_active ? 'ON' : 'OFF' }}
                            </button>
                        </form>
                    </td>
                    <td class="project-actions text-right">
                        <a class="btn btn-primary btn-sm" href="{{route('role.show',Crypt::encrypt($data->id))}}">
                            <i class="fas fa-folder">
                            </i>
                            View
                        </a>
                        <a class="btn btn-info btn-sm" href="{{route('role.edit',Crypt::encrypt($data->id))}}">
                            <i class="fas fa-pencil-alt">
                            </i>
                            Edit
                        </a>
                        <a class="btn btn-danger btn-sm" href="{{route('role.destroy',Crypt::encrypt($data->id))}}" class="btn btn-danger" data-confirm-delete="true">
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