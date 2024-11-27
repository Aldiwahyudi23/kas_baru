@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12">
        <!-- select2bs4 EXAMPLE -->
        <div class="card card-default ">
            <div class="card-header">
                <h3 class="card-title">Edit Data Sub Menu</h3>

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
                <form action="{{ route('sub-menu.update',Crypt::encrypt($DataSubMenu->id)) }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @method('PATCH')
                    {{csrf_field()}}

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Menu <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4 @error('menu_id') is-invalid @enderror" style="width: 100%;" name="menu_id">
                                    <option value="">--Pilih Route--</option>
                                    @foreach($menu as $data)
                                    <option value="{{$data->id}}" {{old('menu_id',$DataSubMenu->menu_id) == $data->id ? 'selected' : '' }}
                                        @if($data->is_active == 0) disabled @endif>
                                        {{ $data->name }}
                                        @if($data->is_active == 0) (Tidak Aktif) @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="name">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{old('name',$DataSubMenu->name)}}" class="form-control col-12 @error('name') is-invalid @enderror">
                            </div>

                            <div class="form-group">
                                <label for="icon">Icon <i class="{{$DataSubMenu->icon}}"></i> <span class="text-danger">*</span> </label>
                                <input type="text" name="icon" id="icon" value="{{old('icon', $DataSubMenu->icon)}}" class="form-control col-12 @error('icon') is-invalid @enderror">
                            </div>

                            <div class="form-group my-colorpicker2">
                                <label for="color">Warna <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('color') is-invalid @enderror" name="color" value="{{old('color', $DataSubMenu->color)}}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-square"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">

                            <div class="form-group">
                                <label>Route URL <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4 @error('route_url_id') is-invalid @enderror" style="width: 100%;" name="route_url_id">
                                    @if(old('route_url_id',$DataSubMenu->route_url_id) == true)
                                    <option selected="selected" value=" {{old('route_url_id',$DataSubMenu->route_url_id)}}">{{old('route_url_id',$DataSubMenu->routeUrl->name)}}</option>
                                    @endif
                                    <option value="">--Pilih Route--</option>
                                    @foreach($AllRouteUrl as $data)
                                    <option value="{{$data->id}}" {{old('route_url_id',$DataSubMenu->route_url_id) == $data->id ? 'selected' : '' }}>
                                        {{$data->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="description" class="col-sm-2 col-form-label">Deskripsi <span class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <textarea class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror" name="description" id="description">{{ old('description', $DataSubMenu->description) }}</textarea>
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
        @include('admin.master_data.data_sub_menu.tabel')
        <!-- /.card -->
    </div>
</div>
@endsection

@section('script')


@endsection