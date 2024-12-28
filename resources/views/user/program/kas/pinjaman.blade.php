@extends('user.layout.app')

@section('content')

<div class="card bg-primary text-white shadow">
    <div class="card-body">
        <h3> <B>
                Rp {{number_format( $saldo_pinjam->saldo ?? 0  - $saldo_proses,0,',','.')}}
            </B></h3>
        @if ($saldo_proses >= 1)
        <div class="text-danger-50 small"> Rp
            - {{number_format( $saldo_proses ?? 0,0,',','.')}} ( Pengajuan Pinjaman sedang Proses )
        </div>
        @endif
        <div class="text-white-50 small"> Rp
            {{number_format( $saldo_pinjam->amount ?? 0,0,',','.')}} ( {{$saldo_pinjam->saldos->code ?? ''}} )
        </div>
        <h6 class="mt-3 mb-0">Saldo yang sedang dipinjam: Rp {{number_format( $saldo_terpakai ?? 0,0,',','.')}}</h6>
    </div>
    <a href="{{Route('saldo.anggaran', ['type' => 'Dana Pinjam'])}}" class="card-footer text-white clearfix small z-1">
        <span class="float-left">Lihat Detail</span>
        <span class="float-right">
            <i class="fas fa-angle-right"></i>
        </span>
    </a>
</div>
<!-- ./row -->
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill"
                            href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home"
                            aria-selected="true">Pengajuan Pinjaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill"
                            href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile"
                            aria-selected="false">Deskripsi</a>
                    </li>
                </ul>
                @if ($pinjaman_proses->count() >= 1)
                @if ($pinjaman_proses->first()->status == "pending")
                <div class="card-tools">
                    <a class="btn btn-tool"
                        href="{{route('pinjaman.edit',Crypt::encrypt($pinjaman_proses->first()->id))}}">
                        <i class="fas fa-pencil-alt">
                        </i>
                    </a>
                    <a class="btn btn-tool"
                        href="{{route('pinjaman.destroy',Crypt::encrypt($pinjaman_proses->first()->id))}}"
                        data-confirm-delete="true">
                        <i class="fas fa-trash">
                        </i>
                    </a>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                @endif
                @endif
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-four-tabContent">
                    <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel"
                        aria-labelledby="custom-tabs-four-home-tab">
                        @if ($pinjaman_proses->count() >= 1)
                        {!! $layout_form->pinjam_proses !!}
                        <hr>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Proses Pengajuan </h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body"></div>
                            <!-- The timeline -->
                            <div class="timeline timeline-inverse">
                                <!-- timeline item -->
                                <div>
                                    <i class="fas fa-user bg-info"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i>
                                            {{$pinjaman_proses->first()->created_at->format('H:i')}}
                                        </span>
                                        <h3 class="timeline-header border-0">Di Ajukan
                                            <b>{{$pinjaman_proses->first()->sekretaris->name}}</b>
                                        </h3>
                                    </div>
                                </div>
                                <!-- END timeline item -->
                                @if ($pinjaman_proses->first()->approved_by)
                                <!-- timeline item -->
                                <div>
                                    <i class="fas fa-user bg-info"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($pinjaman_proses->first()->approved_date ?? '')->format('H:i') }}
                                        </span>
                                        <h3 class="timeline-header border-0">DiKonfirmasi oleh
                                            <b>{{$pinjaman_proses->first()->ketua->name}}</b>
                                        </h3>
                                    </div>
                                </div>
                                @endif
                                @if ($pinjaman_proses->first()->disbursed_by)

                                <!-- timeline item -->
                                <div>
                                    <i class="fas fa-user bg-info"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($pinjaman_proses->first()->disbursed_date ?? '')->format('H:i') }}
                                        </span>
                                        <h3 class="timeline-header border-0">Pencairan oleh
                                            <b>{{$pinjaman_proses->first()->bendahara->name}}</b>
                                        </h3>
                                    </div>
                                </div>
                                @endif

                                <div>
                                    <i class="far fa-clock bg-gray"></i>
                                    <div class="timeline-item">
                                        <div class="timeline-body">

                                            @if($pinjaman_proses->first()->status === 'Acknowledged')
                                            <span class="badge badge-success">Uang sudah diterima</span>
                                            @elseif($pinjaman_proses->first()->status === 'pending')
                                            <span class="badge badge-warning">Menunggu persetujuan Ketua</span>
                                            @elseif($pinjaman_proses->first()->status === 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                            @elseif($pinjaman_proses->first()->status === 'approved_by_chairman')
                                            <span class="badge badge-secondary">Proses Pencairan oleh Bendahara</span>
                                            @elseif($pinjaman_proses->first()->status === 'In Repayment')
                                            <span class="badge badge-success">Proses Cicil</span>
                                            @elseif($pinjaman_proses->first()->status === 'Paid in Full')
                                            <span class="badge badge-success">Selesai / Lunas</span>
                                            @elseif($pinjaman_proses->first()->status === 'disbursed_by_treasurer')
                                            <span class="badge badge-primary">Sudah di cairkan </span>
                                            <p>
                                                Segera cek Rekening atau ambil uang sesuai kesepakatan,
                                            </p>
                                            <b>Segera Konfirmasi jika uang sudah di terima</b>
                                            @else
                                            <span class="badge badge-light">Unknown</span>
                                            <!-- default if status is undefined -->
                                            @endif
                                        </div>
                                        @if ($pinjaman_proses->first()->status == "disbursed_by_treasurer")
                                        <div class="timeline-footer">
                                            <form
                                                action="{{ route('pinjaman.acknowledged',Crypt::encrypt($pinjaman_proses->first()->id)) }}"
                                                method="POST" enctype="multipart/form-data" id="adminForm">
                                                @method('PATCH')
                                                {{csrf_field()}}
                                                <input type="hidden" name="status" value="Acknowledged">
                                                <!-- Button Submit -->
                                                <button type="submit" class="btn btn-success" id="submitBtns">Konfirmasi
                                                    Uang sudah di
                                                    terima</button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a class="btn btn-info btn-sm"
                        href="{{route('pinjaman.show',Crypt::encrypt($pinjaman_proses->first()->id))}}">
                        Lihat Pengajuan
                    </a>

                    @else
                    <!-- Akses kanggo form biasa -->
                    <center>
                        <img src="{{asset($layout_form->icon_pinjaman)}}" alt="" class="img-fluid mb-10 justify-between"
                            width="50%">
                    </center>
                    @include('user.program.kas.form.pinjaman')
                    @endif
                </div>
                <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel"
                    aria-labelledby="custom-tabs-four-profile-tab">
                    <!-- Komponen Deskripsi -->
                    {!!$anggaran->description!!}
                </div>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>

<div class="col-12 col-sm-6">
    <!-- Mengambil pinjaman_proses->first() tabel  -->
    @include('user.program.kas.tabel.pinjaman.anggota')
</div>
<div class="col-12 col-sm-6">
    <!-- Mengambil pinjaman_proses->first() tabel  -->
    @include('user.program.kas.tabel.pinjaman.all')
</div>
</div>
<!-- /.row -->

@endsection