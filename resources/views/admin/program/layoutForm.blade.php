@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->
<div class="alert alert-info alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-info"></i> Info!</h5>
    Di bawah adalah halaman untuk merubah atau mengganti setiap pemberitahuan di halaman admin, Rubah sesuai dengan
    kondisinya
</div>
<form action="{{ route('layouts-form.update',Crypt::encrypt($layoutForm->id)) }}" method="POST"
    enctype="multipart/form-data" id="adminForm">
    @method('PATCH')
    {{csrf_field()}}

    <div class="row">
        <div class=" col-sm-6 col-12">
            <!-- select2bs4 EXAMPLE -->
            <div class="card card-default ">
                <div class="card-header">
                    <h3 class="card-title">Tampilan untuk Pemberitahuan KAS</h3>

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
                    <div class="form-group col-6 col-sm-2">
                        <a href="{{ asset($layoutForm->icon_kas) }}" data-toggle="lightbox" data-title="Icon"
                            data-gallery="gallery">
                            <img src="{{ asset($layoutForm->icon_kas) }}" class="img-fluid mb-2" alt="white sample" />
                        </a>
                    </div>

                    <div class="form-group">
                        <label for="icon_kas">Upload Foto Kas</label>
                        <input type="file" name="icon_kas" id="icon_kas" value="{{old('icon_kas')}}"
                            class="form-control col-12 @error('icon_kas') is-invalid @enderror">
                    </div>

                    <div class="form-group">
                        <label for="kas_proses" class="col-sm-12 col-form-label">Pemberitahuan status
                            proses</label>
                        <div class="col-sm-12">
                            <textarea
                                class="summernote-textarea form-control col-sm-12 @error('kas_proses') is-invalid @enderror"
                                name="kas_proses"
                                id="kas_proses">{{ old('kas_proses',$layoutForm->kas_proses) }}</textarea>
                        </div>
                    </div>

                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                    </p>
                </div>
            </div>
            <!-- /.card -->
        </div>
        <div class=" col-sm-6 col-12">
            <!-- select2bs4 EXAMPLE -->
            <div class="card card-default ">
                <div class="card-header">
                    <h3 class="card-title">Tampilan untuk Pemberitahuan KAS</h3>

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
                    <div class="form-group col-6 col-sm-2">
                        <a href="{{ asset($layoutForm->icon_tabungan) }}" data-toggle="lightbox" data-title="Icon"
                            data-gallery="gallery">
                            <img src="{{ asset($layoutForm->icon_tabungan) }}" class="img-fluid mb-2"
                                alt="white sample" />
                        </a>
                    </div>

                    <div class="form-group">
                        <label for="icon_tabungan">Upload Foto Tabungan </label>
                        <input type="file" name="icon_tabungan" id="icon_tabungan" value="{{old('icon_tabungan')}}"
                            class="form-control col-12 @error('icon_tabungan') is-invalid @enderror">
                    </div>

                    <div class="form-group">
                        <label for="tabungan_proses" class="col-sm-12 col-form-label">Pemberitahuan Status
                            Proses</label>
                        <div class="col-sm-12">
                            <textarea
                                class="summernote-textarea form-control col-sm-12 @error('tabungan_proses') is-invalid @enderror"
                                name="tabungan_proses"
                                id="tabungan_proses">{{ old('tabungan_proses',$layoutForm->tabungan_proses) }}</textarea>
                        </div>
                    </div>
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
    <div class="row">
        <div class=" col-sm-6 col-12">
            <!-- select2bs4 EXAMPLE -->
            <div class="card card-default ">
                <div class="card-header">
                    <h3 class="card-title">Untuk Bayar Pinjaman</h3>

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
                    <div class="form-group col-6 col-sm-2">
                        <a href="{{ asset($layoutForm->icon_b_pinjam) }}" data-toggle="lightbox" data-title="Icon"
                            data-gallery="gallery">
                            <img src="{{ asset($layoutForm->icon_b_pinjam) }}" class="img-fluid mb-2"
                                alt="white sample" />
                        </a>
                    </div>

                    <div class="form-group">
                        <label for="icon_b_pinjam">Upload Foto Kas</label>
                        <input type="file" name="icon_b_pinjam" id="icon_b_pinjam" value="{{old('icon_b_pinjam')}}"
                            class="form-control col-12 @error('icon_b_pinjam') is-invalid @enderror">
                    </div>

                    <div class="form-group">
                        <label for="b_pinjam_proses" class="col-sm-12 col-form-label">Pemberitahuan status
                            b_pinjam</label>
                        <div class="col-sm-12">
                            <textarea
                                class="summernote-textarea form-control col-sm-12 @error('b_pinjam_proses') is-invalid @enderror"
                                name="b_pinjam_proses"
                                id="b_pinjam_proses">{{ old('b_pinjam_proses',$layoutForm->b_pinjam_proses) }}</textarea>
                        </div>
                    </div>

                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                    </p>
                </div>
            </div>
            <!-- /.card -->
        </div>
        <div class=" col-sm-6 col-12">
            <!-- select2bs4 EXAMPLE -->
            <div class="card card-default ">
                <div class="card-header">
                    <h3 class="card-title">Untuk Pinjaman</h3>

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
                    <div class="form-group col-6 col-sm-2">
                        <a href="{{ asset($layoutForm->icon_pinjam) }}" data-toggle="lightbox" data-title="Icon"
                            data-gallery="gallery">
                            <img src="{{ asset($layoutForm->icon_pinjam) }}" class="img-fluid mb-2"
                                alt="white sample" />
                        </a>
                    </div>

                    <div class="form-group">
                        <label for="icon_pinjam">Upload Foto Tabungan </label>
                        <input type="file" name="icon_pinjam" id="icon_pinjam" value="{{old('icon_pinjam')}}"
                            class="form-control col-12 @error('icon_pinjam') is-invalid @enderror">
                    </div>

                    <div class="form-group">
                        <label for="pinjam_proses" class="col-sm-12 col-form-label">Pemberitahuan Status Pinjaman masih
                            proses
                            Proses</label>
                        <div class="col-sm-12">
                            <textarea
                                class="summernote-textarea form-control col-sm-12 @error('pinjam_proses') is-invalid @enderror"
                                name="pinjam_proses"
                                id="pinjam_proses">{{ old('pinjam_proses',$layoutForm->pinjam_proses) }}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pinjam_saldo" class="col-sm-12 col-form-label">Pemberitahuan Kalau saldo belum cukup
                            untuk di pinjam
                            Proses</label>
                        <div class="col-sm-12">
                            <textarea
                                class="summernote-textarea form-control col-sm-12 @error('pinjam_saldo') is-invalid @enderror"
                                name="pinjam_saldo"
                                id="pinjam_saldo">{{ old('pinjam_saldo',$layoutForm->pinjam_saldo) }}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pinjam_penuh" class="col-sm-12 col-form-label">Pemberitahuan Untuk Pinjaman sudah
                            Penuh/Saldo habis
                            Proses</label>
                        <div class="col-sm-12">
                            <textarea
                                class="summernote-textarea form-control col-sm-12 @error('pinjam_penuh') is-invalid @enderror"
                                name="pinjam_penuh"
                                id="pinjam_penuh">{{ old('pinjam_penuh',$layoutForm->pinjam_penuh) }}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pinjam_nunggak" class="col-sm-12 col-form-label">Pemberitahuan Untuk yang masih ada
                            Tunggakan
                            Proses</label>
                        <div class="col-sm-12">
                            <textarea
                                class="summernote-textarea form-control col-sm-12 @error('pinjam_nunggak') is-invalid @enderror"
                                name="pinjam_nunggak"
                                id="pinjam_nunggak">{{ old('pinjam_nunggak',$layoutForm->pinjam_nunggak) }}</textarea>
                        </div>
                    </div>
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

    <!-- Button Submit -->
    <button type="submit" class="btn btn-success" id="submitBtns">Update</button>
</form>
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