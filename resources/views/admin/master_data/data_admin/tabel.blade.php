<!-- Kode ini untuk isi tabel di dalam index data_admin -->

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Admin</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No Hp</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($dataAdmin as $data)
                <?php $no++; ?>
                <tr>
                    <td>{{$no}} </td>
                    <td>
                        <img class="profile-user-img img-fluid img-circle"
                            src="{{ asset('storage/' . $data->profile_photo_path) }}" alt="User profile picture">
                    </td>
                    <td>{{$data->name}} </td>
                    <td>{{$data->email}} </td>
                    <td>{{$data->phone_number}} </td>
                    <td>
                        @if($data->id !== Auth::id())
                        <form class="statusAdmin" method="POST">
                            {{ csrf_field() }}
                            <button type="button"
                                class="btn {{ $data && $data->is_active ? 'btn-success' : 'btn-danger' }}"
                                data-url="{{ route('admin.toggleStatus', Crypt::encrypt($data->id)) }}"
                                data-active="{{ $data && $data->is_active ? 1 : 0 }}" onclick="toggleAccess(this)">
                                {{ $data && $data->is_active ? 'ON' : 'OFF' }}
                            </button>
                        </form>
                        @endif


                    </td>
                    <td class="project-actions text-right">
                        <a class="btn btn-primary btn-sm" href="{{route('data-admin.show',Crypt::encrypt($data->id))}}">
                            <i class="fas fa-folder">
                            </i>
                            View
                        </a>
                        <a class="btn btn-info btn-sm" href="{{route('data-admin.edit',Crypt::encrypt($data->id))}}">
                            <i class="fas fa-pencil-alt">
                            </i>
                            Edit
                        </a>
                        <a class="btn btn-danger btn-sm"
                            href="{{route('data-admin.destroy',Crypt::encrypt($data->id))}}" class="btn btn-danger"
                            data-confirm-delete="true">
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