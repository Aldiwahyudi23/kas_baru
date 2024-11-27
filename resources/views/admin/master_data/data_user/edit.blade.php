@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12">
        <!-- select2bs4 EXAMPLE -->
        <div class="card card-default ">
            <div class="card-header">
                <h3 class="card-title">Edit Data User</h3>

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
                <form action="{{ route('menu.update',Crypt::encrypt($DataMenu->id)) }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @method('PATCH')
                    {{csrf_field()}}

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" name="name" id="name" value="{{old('name',$DataMenu->name)}}" class="form-control col-12 @error('name') is-invalid @enderror">
                            </div>

                            <div class="form-group">
                                <label for="icon">Icon <i class="{{$DataMenu->icon}}"></i> </label>
                                <input type="text" name="icon" id="icon" value="{{old('icon', $DataMenu->icon)}}" class="form-control col-12 @error('icon') is-invalid @enderror">
                            </div>

                            <div class="form-group my-colorpicker2">
                                <label for="color">Warna</label>
                                <input type="text" class="form-control @error('color') is-invalid @enderror" name="color" value="{{old('color', $DataMenu->color)}}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-square"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">

                            <div class="form-group">
                                <label>Route URL</label>
                                <select class="form-control select2bs4 @error('route_url_id') is-invalid @enderror" style="width: 100%;" name="route_url_id">
                                    <option value="">--Pilih Route--</option>
                                    @foreach($AllRouteUrl as $data)
                                    <option value="{{$data->id}}" {{old('route_url_id',$DataMenu->route_url_id) == $data->id ? 'selected' : ''}}>
                                        {{$data->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="description" class="col-sm-2 col-form-label">Deskripsi</label>
                                <div class="col-sm-12">
                                    <textarea class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror" name="description" id="description">{{ old('description', $DataMenu->description) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Update</button>
                </form>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
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
        @include('admin.master_data.data_menu.tabel')
        <!-- /.card -->
    </div>
</div>
@endsection

@section('script')


@endsection