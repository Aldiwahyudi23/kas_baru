@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->
<div class="row">
    <div class="col-12">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default {{ $errors->any() ? '' : 'collapsed-card' }}">
            <div class="card-header">
                <h3 class="card-title">Tambah Data Acees</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <!-- Data ini di ambil dari file terpisah view/admin/master_data/data_admin/tabel -->
            @include('admin.notification.create')
        </div>
        <!-- /.card -->
    </div>
</div>
<div class="row">
    <div class="col-12">
        <!-- Data ini di ambil dari file terpisah view/admin/master_data/data_admin/tabel -->
        @include('admin.notification.tabel_access')
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->

@endsection

@section('script')


@endsection