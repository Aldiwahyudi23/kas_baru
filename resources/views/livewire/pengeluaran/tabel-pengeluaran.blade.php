<div>
    @if ($cashExpenditures)
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Tanggal Input</th>
                    <th>Anggaran</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($cashExpenditures as $data)
                <?php $no++; ?>
                <tr>
                    <td>{{$no}} </td>
                    <td>{{$data->code}} </td>
                    <td>{{$data->created_at}} </td>
                    <td>{{$data->anggaran->name}} </td>
                    <td>{{$data->amount}} </td>
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
                        @else
                        <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                        @endif
                    </td>
                    <td class="project-actions text-right">
                        <a class="btn btn-primary btn-sm"
                            href="{{route('expenditure.show',Crypt::encrypt($data->id))}}">
                            <i class="fas fa-folder">
                            </i>
                            View
                        </a>
                        <a class="btn btn-info btn-sm" href="{{route('expenditure.edit',Crypt::encrypt($data->id))}}">
                            <i class="fas fa-pencil-alt">
                            </i>
                            Edit
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>