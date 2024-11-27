@extends('user.layout.app')

@section('content')
<!-- Info boxes -->

<!-- /.row -->
<div class="row">
    <div class="col-12">
        <!-- Data ini di ambil dari file terpisah view/admin/master_data/data_admin/tabel -->
        <div class="card-body">
            <form action="{{ route('notif') }}" method="POST" enctype="multipart/form-data" id="adminForm">
                @csrf
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input type="text" name="name" id="name" value="{{old('name')}}"
                                class="form-control col-12 @error('name') is-invalid @enderror">
                        </div>


                        <div class="form-group">
                            <label for="phone">phone</label>
                            <input type="number" name="phone" id="phone" value="{{old('phone')}}"
                                class="form-control col-12 @error('phone') is-invalid @enderror">
                        </div>
                        <div class="form-group">
                            <label for="amount">amount</label>
                            <input type="number" name="amount" id="amount" value="{{old('amount')}}"
                                class="form-control col-12 @error('amount') is-invalid @enderror">
                        </div>


                    </div>
                    <div class="col-12 col-sm-6">

                        <div class="form-group">
                            <label for="description" class="col-sm-2 col-form-label">Deskripsi</label>
                            <div class="col-sm-12">
                                <textarea
                                    class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror"
                                    name="description" id="description">{{ old('description') }}</textarea>
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
            </p>

        </div>
        <!-- /.card -->
    </div>
</div>
@endsection

@section('script')


@endsection