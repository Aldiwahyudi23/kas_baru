@extends('user.layout.app')

@section('content')

@if ($pinjaman->status != "Acknowledged")
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Penting !</h5>
    halaman ini untuk membayar pinjaman yang masih aktif
</div>
@endif
@if ($pinjaman->status == "Acknowledged")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Data Pengeluaran sudah selesai.
</div>
@endif
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">{{$pinjaman->anggaran->name}} ( {{$pinjaman->code}} )</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <tbody>
                    <tr>
                        <td>Kode</td>
                        <td>:</td>
                        <td>{{$pinjaman->code}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>:</td>
                        <td>{{$pinjaman->created_at}}</td>
                    </tr>
                    <tr>
                        <td>Di Input oleh</td>
                        <td>:</td>
                        <td>{{$pinjaman->sekretaris->name}}</td>
                    </tr>
                    <tr>
                        <td>Data Warga</td>
                        <td>:</td>
                        <td>{{$pinjaman->warga->name}}</td>
                    </tr>
                    <tr>
                        <td>Nominal</td>
                        <td>:</td>
                        <td>Rp. {{number_format( $pinjaman->loan_amount, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>
                            @if($pinjaman->status === 'Acknowledged')
                            <span class="badge badge-success">Uang sudah diterima</span>
                            @elseif($pinjaman->status === 'pending')
                            <span class="badge badge-warning">Menunggu persetujuan Ketua</span>
                            @elseif($pinjaman->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                            @elseif($pinjaman->status === 'approved_by_chairman')
                            <span class="badge badge-secondary">Proses Pencairan oleh Bendahara</span>
                            @elseif($pinjaman->status === 'disbursed_by_treasurer')
                            <span class="badge badge-secondary">Sudah di cairkan </span>
                            @elseif($pinjaman->status === 'In Repayment')
                            <span class="badge badge-success">Dalah Proses Cicil</span>
                            @elseif($pinjaman->status === 'Paid in Full')
                            <span class="badge badge-success">Selesai/Lunas</span>
                            @else
                            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="card-footer">
                <p>
                    Keterangan : <br>
                    {!!$pinjaman->description!!}
                </p>
            </div>
        </div>
        <hr>



        <div class="card card-primary card-outline">
            <div class="card-body box-profile">

                <h3 class="profile-username text-center">Bayar Pinjaman</h3>
                <p class="text-muted text-center">{{$pinjaman->code}}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    @foreach ($bayarPinjaman as $data)
                    <li class="list-group-item">
                        <b>{{$data->created_at}}</b> <a class="float-right">Rp. {{number_format( $data->amount, 0, ',', '.')}}</a>
                    </li>
                    @endforeach
                </ul>

               
            </div>
            <!-- /.card-body -->
        </div>


        @if (in_array($pinjaman->status, ['Acknowledged', 'In Repayment']))
        <!-- untuk ketua -->
        @if(in_array(Auth::user()->role->name, ['Ketua','Wakil Ketua','Bendahara','Wakil Bendahara','Sekretaris','Wakil
        Sekretaris']) || Auth::user()->data_warga_id == $pinjaman->data_warga_id)

        @if (isset($cek_pembayaran))
        <hr>
        {!!$layout_form->b_pinjam_proses!!}
        @else
        @include('user.program.kas.form.bayarPinjaman')
        @endif

        @endif
        @endif
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <p>
            Catatan : <br>
            - Data yang di input di sesuaikan dengan anggaran yang sudah di sepakati <br>
            - Pastikan data sesuai <br>
        </p>
        <p><i>Anggaran yang sudah di ajukan semoga bermanfaat</i></p>
    </div>
</div>
<!-- /.card -->


@endsection