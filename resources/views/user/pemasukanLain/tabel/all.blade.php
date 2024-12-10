<!-- Kode ini untuk isi tabel di dalam index data_admin -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data pemasukan Lain</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable ">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Tanggal Input</th>
                    <th>Nama Warga</th>
                    <th>Status</th>
                    <th>Di Konfirmasi Oleh</th>
                    <th>Tanggal Konfirmasi</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($data_income as $data)
                <?php $no++; ?>
                <tr>
                    <td>{{$no}} </td>
                    <td>{{$data->code}} </td>
                    <td>{{$data->payment_date}} </td>
                    <td>{{$data->anggaran->name}} </td>
                    <td>
                        @if($data->status === 'confirmed')
                        <span class="badge badge-success">Confirmed</span>
                        @elseif($data->status === 'pending')
                        <span class="badge badge-warning">Pending</span>
                        @elseif($data->status === 'rejected')
                        <span class="badge badge-danger">Rejected</span>
                        @elseif($data->status === 'process')
                        <span class="badge badge-secondary">Process</span>
                        @else
                        <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                        @endif
                    </td>
                    <td>{{$data->confirmed->name ?? null}} </td>
                    <td> {{$data->confirmation_date}} </td>
                    <td class="project-actions text-right">
                        <a class="btn btn-primary btn-sm"
                            href="{{route('other-income.show',Crypt::encrypt($data->id))}}">
                            <i class="fas fa-folder">
                            </i>
                            View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>