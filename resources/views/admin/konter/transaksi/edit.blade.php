@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12">
        <!-- select2bs4 EXAMPLE -->
        <div class="card card-default ">
            <div class="card-header">
                <h3 class="card-title">Edit Data Program</h3>

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
                <form action="{{ route('program.update',Crypt::encrypt($DataProgram->id)) }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @method('PATCH')
                    {{csrf_field()}}

                    <div class="row">
                        <div class="col-12 col-sm-6">

                            <div class="form-group">
                                <label for="name">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{old('name',$DataProgram->name)}}" class="form-control col-12 @error('name') is-invalid @enderror">
                            </div>

                            <div class="form-group">
                                <label for="created">Tanggal di Buat <span class="text-danger">*</span> </label>
                                <input type="date" name="created" id="created" value="{{old('created', $DataProgram->created)}}" class="form-control col-12 @error('created') is-invalid @enderror">

                                <label class="small-text">- Program ini di buat pada tanggal ({{$DataProgram->created}}) <br> - Kosongkan jika tidak mau diubah</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">

                            <div class="form-group">
                                <label for="snk" class="col-sm-2 col-form-label">Syarat dan Ketentuan <span class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <textarea class="summernote-textarea form-control col-12 @error('snk') is-invalid @enderror" name="snk" id="snk">{{ old('snk', $DataProgram->snk) }}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description" class="col-sm-2 col-form-label">Deskripsi <span class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <textarea class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror" name="description" id="description">{{ old('description', $DataProgram->description) }}</textarea>
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
                    <br>- Bertanda bintang Merah wajib di isi.
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
        @include('admin.master_data.data_program.tabel')
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