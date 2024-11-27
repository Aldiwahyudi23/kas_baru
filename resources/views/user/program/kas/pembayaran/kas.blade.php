@extends('user.layout.app')

@section('content')

<!-- ./row -->
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill"
                            href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home"
                            aria-selected="true">Bayar KAS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill"
                            href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile"
                            aria-selected="false">Deskripsi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-four-messages-tab" data-toggle="pill"
                            href="#custom-tabs-four-messages" role="tab" aria-controls="custom-tabs-four-messages"
                            aria-selected="false">S & K</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-four-tabContent">
                    <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel"
                        aria-labelledby="custom-tabs-four-home-tab">
                        @if ($cek_kasPayment >= 1)
                        {!! $layout_form->kas_proses !!}
                        <br>
                        <a class="btn btn-info btn-sm"
                            href="{{route('kas.edit',Crypt::encrypt($pembayaran_proses->id))}}">
                            <i class="fas fa-pencil-alt">
                            </i>
                            Edit Pembayaran
                        </a>
                        <a class="btn btn-danger btn-sm"
                            href="{{route('kas.destroy',Crypt::encrypt($pembayaran_proses->id))}}"
                            data-confirm-delete="true">
                            <i class="fas fa-trash">
                            </i>
                            Delete
                        </a>
                        @else
                        <!-- Akses kanggo form biasa -->
                        <center>
                            <img src="{{asset('storage/'.$layout_form->icon_kas)}}" alt=""
                                class="img-fluid mb-10 justify-between" width="50%">
                        </center>

                        @include('user.program.kas.form.kas')
                        @endif
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel"
                        aria-labelledby="custom-tabs-four-profile-tab">
                        {!!$program->description!!}
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-four-messages" role="tabpanel"
                        aria-labelledby="custom-tabs-four-messages-tab">
                        {!!$program->snk!!}
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>

    <div class="col-12 col-sm-6">
        <!-- Mengambil data tabel  -->
        @include('user.program.kas.tabel.kas.anggota')
    </div>
</div>
<!-- /.row -->

@endsection