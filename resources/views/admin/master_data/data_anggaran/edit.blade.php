@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12">
        <!-- select2bs4 EXAMPLE -->
        <div class="card card-default ">
            <div class="card-header">
                <h3 class="card-title">Edit Data Anggaran</h3>

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
                <form action="{{ route('anggaran.update',Crypt::encrypt($DataAnggaran->id)) }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @method('PATCH')
                    {{csrf_field()}}

                    <div class="row">
                        <div class="col-12 col-sm-6">

                            <div class="form-group">
                                <label for="program_id" class="com-sm-2 col-form-label">Program</label>
                                <select class="form-control select2bs4 @error('program_id') is-invalid @enderror" style="width: 100%;" name="program_id">
                                    @if(old('program_id',$DataAnggaran->program_id) == true)
                                    <option selected="selected" value=" {{old('program_id',$DataAnggaran->program_id)}}">{{old('program_id',$DataAnggaran->program->name)}}</option>
                                    @endif
                                    <option value="">--Pilih Route--</option>
                                    @foreach($program as $data)
                                    <option value="{{$data->id}}" {{old('program_id',$DataAnggaran->program_id) == $data->id ? 'selected' : ''}}>
                                        {{$data->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" name="name" id="name" value="{{old('name',$DataAnggaran->name)}}" class="form-control col-12 @error('name') is-invalid @enderror">
                            </div>
                            <div class="form-group">
                                <label for="code_anggaran">Kode Anggaran</label>
                                <input type="text" name="code_anggaran" id="code_anggaran" value="{{old('code_anggaran',$DataAnggaran->code_anggaran)}}" class="form-control col-12 @error('code_anggaran') is-invalid @enderror">
                            </div>

                        </div>
                        <div class="col-12 col-sm-6">

                            <div class="form-group">
                                <label for="description" class="col-sm-2 col-form-label">Deskripsi</label>
                                <div class="col-sm-12">
                                    <textarea class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror" name="description" id="description">{{ old('description',$DataAnggaran->description) }}</textarea>
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
        @include('admin.master_data.data_anggaran.tabel')
        <!-- /.card -->
    </div>
</div>
@endsection

@section('script')


@endsection

@section('style')
<style>
    .small-text {
        font-size: 12px;
        color: red;
        display: block;
        /* Aggar label di bawah input */
        margin-top: 5px;
    }
</style>

@endsection