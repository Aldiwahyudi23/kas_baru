<!-- Kode ini untuk isi tabel di dalam index data_admin -->

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Route Url</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Nama Route</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($AllRouteUrl as $data)
                <?php $no++; ?>
                <tr>
                    <td>{{$no}} </td>
                    <td>{{$data->code}} </td>
                    <td>{{$data->name}} </td>
                    <td>{{$data->route_name}} </td>
                    <td class="project-actions text-right">
                        <a class="btn btn-primary btn-sm" href="{{route('all-route-url.show',Crypt::encrypt($data->id))}}">
                            <i class="fas fa-folder">
                            </i>
                            View
                        </a>
                        <a class="btn btn-info btn-sm" href="{{route('all-route-url.edit',Crypt::encrypt($data->id))}}">
                            <i class="fas fa-pencil-alt">
                            </i>
                            Edit
                        </a>
                        <a class="btn btn-danger btn-sm" href="{{route('all-route-url.destroy',Crypt::encrypt($data->id))}}" class="btn btn-danger" data-confirm-delete="true">
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