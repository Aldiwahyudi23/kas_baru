@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default {{ $errors->any() ? '' : 'collapsed-card' }}">
            <div class="card-header">
                <h3 class="card-title">Tambah Data Route</h3>

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
            <div class="card-body">
                <form action="{{ route('all-route-url.store') }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="name">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{old('name')}}" class="form-control col-12 @error('name') is-invalid @enderror">
                            </div>

                            <div class="form-group">
                                <label>Nama Route <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4 @error('route_name') is-invalid @enderror" style="width: 100%;" name="route_name">
                                    <option value="">-- Pilih Route --</option>
                                    @foreach($routes as $route)
                                    <option value="{{ $route }}" {{old('route_name') == $route ? 'selected' : '' }}>
                                        {{ $route }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="description" class="col-sm-2 col-form-label">Deskripsi <span class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <textarea class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror" name="description" id="description">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Create</button>
                </form>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                    <br>- Bertanda bintang merah wajib di isi.
                </p>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->
<div class="row">
    <div class="col-12">
        <!-- Data ini di ambil dari file terpisah view/admin/master_data/data_admin/tabel -->
        @include('admin.master_data.data_route_url.tabel')
        <!-- /.card -->
    </div>
</div>
@endsection

@section('script')


@endsection

@section('style')
<style>
    .is-valid {
        border-color: green;
    }

    .is-invalid {
        border-color: red;
    }

    .feedback {
        font-size: 12px;
        margin-top: 5px;
    }
</style>
@endsection