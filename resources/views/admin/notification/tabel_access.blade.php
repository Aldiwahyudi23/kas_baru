<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Access Notifikasi</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Access</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($DataNotification as $data)
                <?php $no++; ?>
                <tr>
                    <td>{{$no}} </td>
                    <td>
                        <a href="{{route('access-notification.show',Crypt::encrypt($data->id))}}"
                            class="btn btn-success">Setting</a>
                    </td>
                    <td>{{$data->type}} ( {{$data->name}} ) </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>

@section('script')

@endsection