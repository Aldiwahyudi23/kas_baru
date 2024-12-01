@extends('user.layout.app')

@section('content')

<div class="alert alert-info alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-info"></i> Info !</h5>
    Data di bawah adalah data masih dalam status proses yang harus di kofirmasi oleh pengurus
</div>
<!-- SELECT2 EXAMPLE -->
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Pengajuan Pinjaman ke 2 yang perlu di Konfirmasi</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="example2" class="table table-bordered table-hover table-responsive">
                    <thead>
                        <tr>
                            <td>No</td>
                            <th>Status</th>
                            <th>Kode Pinjaman</th>
                            <th>Pengaju</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 0;
                        ?>
                        @foreach ($pinjaman as $data )
                        <?php
                        $no++;
                        ?>
                        <tr onclick="window.location='{{ route('pinjaman-ke-dua.show.confirm',Crypt::encrypt($data->id)) }}'"
                            style="cursor: pointer;">
                            <td>{{$no}}</td>
                            <td>
                                @if($data->status === 'confirmed')
                                <span class="badge badge-success">Selesai</span>
                                @elseif($data->status === 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                                @elseif($data->status === 'pending')
                                <span class="badge badge-warning">Perlu diKonfirmasi</span>
                                @else
                                <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                                @endif
                            </td>
                            <td>{{$data->pinjaman->code}}</td>
                            <td>{{$data->data_warga->name}}</td>

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
    </div>

    <div class="col-12 col-sm-6">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Pengajuan Pinjaman ke 2 yang sudah di reject</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="example2" class="table table-bordered table-hover table-responsive">
                    <thead>
                        <tr>
                            <td>No</td>
                            <th>Status</th>
                            <th>Kode Pinjaman</th>
                            <th>Di Reject</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 0;
                        ?>
                        @foreach ($pinjaman_reject as $data )
                        <?php
                        $no++;
                        ?>
                        <tr onclick="window.location='{{ route('pinjaman-ke-dua.show.confirm',Crypt::encrypt($data->id)) }}'"
                            style="cursor: pointer;">
                            <td>{{$no}}</td>
                            <td>
                                @if($data->status === 'confirmed')
                                <span class="badge badge-success">Selesai</span>
                                @elseif($data->status === 'rejected')
                                <span class="badge badge-danger">Rejected</span>
                                @elseif($data->status === 'pending')
                                <span class="badge badge-secondary">Perlu di Konfirmasi</span>
                                @else
                                <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                                @endif
                            </td>
                            <td>{{$data->pinjaman->code}}</td>
                            <td>{{$data->data_warga->name}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <p>
                    Catatan : <br>
                    - Data yang di atas sudah di batalkan<br>
                    - Data Pengajuan yang sudah di batalkan akan muncul di bawah <br>
                </p>
                <p><i> Klik baris data untuk masuk atau melihat data </i></p>
            </div>
        </div>
    </div>
</div>

<!-- /.card -->


@endsection