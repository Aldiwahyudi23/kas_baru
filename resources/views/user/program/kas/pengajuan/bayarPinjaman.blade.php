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
        <h3 class="card-title">Pengajuan Bayar Pinjaman</h3>

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
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Nominal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bayarPinjaman_proses as $data )
                <tr onclick="window.location='{{ route('bayar-pinjaman.show.confirm',Crypt::encrypt($data->id)) }}'"
                    style="cursor: pointer;">
                    <td>{{$data->code}}</td>
                    <td>{{$data->data_warga->name}}</td>
                    <td>{{$data->amount}}</td>
                    <td>
                        @if($data->status === 'Acknowledged')
                        <span class="badge badge-success">Dikonfirmasi</span>
                        @elseif($data->status === 'process')
                        <span class="badge badge-warning">Menunggu Dikonfirmasi Bendahara</span>
                        @elseif($data->status === 'rejected')
                        <span class="badge badge-danger">Rejected</span>
                        @elseif($data->status === 'pending')
                        <span class="badge badge-secondary">Pending</span>
                        @else
                        <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                        @endif
                    </td>
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