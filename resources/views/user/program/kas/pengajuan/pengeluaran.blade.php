@extends('user.layout.app')

@section('content')

<div class="alert alert-info alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-info"></i> Info !</h5>
    Data di bawah adalah data masih dalam status proses yang harus di kofirmasi oleh pengurus
</div>
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Pengajuan Pengeluaran</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example2" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Anggaran</th>
                    <th>Nominal</th>
                    <th>Kode</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pengeluaran_proses as $data )
                <tr onclick="window.location='{{ route('pengeluaran.show.confirm',Crypt::encrypt($data->id)) }}'"
                    style="cursor: pointer;">
                    <td>
                        @if($data->status === 'Acknowledged')
                        <span class="badge badge-success">Selesai</span>
                        @elseif($data->status === 'pending')
                        <span class="badge badge-warning">Pending</span>
                        @elseif($data->status === 'rejected')
                        <span class="badge badge-danger">Rejected</span>
                        @elseif($data->status === 'approved_by_chairman')
                        <span class="badge badge-secondary">Menunggu persetujuan Ketua</span>
                        @elseif($data->status === 'disbursed_by_treasurer')
                        <span class="badge badge-secondary">Dalam Proses Pencairan</span>
                        @else
                        <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                        @endif
                    </td>
                    <td>{{$data->anggaran->name}}</td>
                    <td>{{$data->amount}}</td>
                    <td>{{$data->code}}</td>

                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <p>
            Catatan : <br>
            - Segera Konfirmasi Pengajuan <br>
            - Pastikan data sesuai <br>
        </p>
        <p><i> Klik baris data untuk masuk atau melihat data untuk di konfirmasi</i></p>
    </div>
</div>
<!-- /.card -->


@endsection