<!-- Kode ini untuk isi tabel di dalam index data_admin -->

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Pinjaman Masih proses</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Tanggal Input</th>
                    <th>Nama Warga</th>
                    <th>Nominal</th>
                    <th>Sisa</th>
                    <th>Lebih</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($pinjaman_proses as $data)
                <?php $no++; ?>
                <tr>
                    <td>{{$no}} </td>
                    <td>{{$data->code}} </td>
                    <td>{{$data->created_at}} </td>
                    <td>{{$data->warga->name}} </td>
                    <td>Rp {{number_format($data->loan_amount, 2,',','.')}} </td>
                    <td>Rp {{number_format($data->remaining_balance, 2,',','.')}} </td>
                    <td>Rp {{number_format($data->overpayment_balance, 2,',','.')}} </td>
                    <td>
                        @if($data->status === 'Acknowledged')
                        <span class="badge badge-success">Acknowledged</span>
                        @elseif($data->status === 'pending')
                        <span class="badge badge-warning">Pending</span>
                        @elseif($data->status === 'rejected')
                        <span class="badge badge-danger">Rejected</span>
                        @elseif($data->status === 'approved_by_chairman')
                        <span class="badge badge-secondary">approved_by_chairman</span>
                        @elseif($data->status === 'disbursed_by_treasurer')
                        <span class="badge badge-secondary">disbursed_by_treasurer</span>
                        @elseif($data->status === 'In Repayment')
                        <span class="badge badge-secondary">In Repayment</span>
                        @elseif($data->status === 'Paid in Full')
                        <span class="badge badge-secondary">Paid in Full</span>
                        @else
                        <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                        @endif
                    </td>
                    <td class="project-actions text-right">
                        <a class="btn btn-primary btn-sm" href="{{route('loan.show',Crypt::encrypt($data->id))}}">
                            <i class="fas fa-folder">
                            </i>
                            View
                        </a>
                        <a class="btn btn-info btn-sm" href="{{route('loan.edit',Crypt::encrypt($data->id))}}">
                            <i class="fas fa-pencil-alt">
                            </i>
                            Edit
                        </a>
                        <a class="btn btn-danger btn-sm" href="{{route('loan.destroy',Crypt::encrypt($data->id))}}" class="btn btn-danger" data-confirm-delete="true">
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