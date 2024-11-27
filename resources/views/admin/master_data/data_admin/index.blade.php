@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default {{ $errors->any() ? '' : 'collapsed-card' }}">
            <div class="card-header">
                <h3 class="card-title">Tambah Data Admin</h3>

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
                <form action="{{ route('data-admin.store') }}" method="POST" enctype="multipart/form-data"
                    id="adminForm">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" name="name" id="name" value="{{old('name')}}"
                                    class="form-control col-12 @error('name') is-invalid @enderror">
                            </div>
                            @error('name')
                            <div class="invalid-feedback">
                                <strong>{{$message}}</strong>
                            </div>
                            @enderror

                            <div class="form-group">
                                <label for="phone_number">No Hp</label>
                                <input type="text" name="phone_number" id="phone_number" value="{{old('phone_number')}}"
                                    class="form-control col-12 @error('phone_number') is-invalid @enderror">
                            </div>
                            @error('phone_number')
                            <div class="invalid-feedback">
                                <strong>{{$message}}</strong>
                            </div>
                            @enderror

                            <div class="form-group">
                                <label for="profile_photo_path">Profile Picture</label>
                                <input type="file" name="profile_photo_path" id="profile_photo_path"
                                    value="{{old('profile_photo_path')}}" class="form-control col-12"
                                    onchange="preview('.tampil-gambar', this.files[0])">
                                <div class="tampil-gambar mt-3">

                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" value="{{old('email')}}"
                                    class="form-control col-12 @error('email') is-invalid @enderror" required>
                            </div>
                            @error('email')
                            <div class="invalid-feedback">
                                <strong>{{$message}}</strong>
                            </div>
                            @enderror

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password"
                                    class="form-control col-12 @error('password') is-invalid @enderror">
                            </div>
                            @error('password')
                            <div class="invalid-feedback">
                                <strong>{{$message}}</strong>
                            </div>
                            @enderror

                            <div class="form-group">
                                <label for="is_active">Status Aktif</label>
                                <select class="form-control col-12 @error('is_active') is-invalid @enderror"
                                    name="is_active">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                                @error('is_active')
                                <div class="invalid-feedback">
                                    <strong>{{$message}}</strong>
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Create Admin</button>
                </form>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                    <br>- Admin hanya bisa ditambahkan max 3.
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
        @include('admin.master_data.data_admin.tabel')
        <!-- /.card -->
    </div>
</div>
@endsection

@section('script')
<script>
const submitBtn = document.getElementById('submitBtn');

// Function to check form validation
function checkForm() {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const phoneNumber = document.getElementById('phone_number').value.trim();

    // Check if all inputs are filled and if password >= 6 and phone_number >= 10
    if (
        name !== '' &&
        email !== '' &&
        password.length >= 6 && // Password must be at least 6 characters
        phoneNumber.length >= 10 // Phone number must be at least 10 characters
    ) {
        submitBtn.disabled = false; // Enable submit button
    } else {
        submitBtn.disabled = true; // Disable submit button
    }
}

// Add event listener to form fields
document.getElementById('name').addEventListener('input', checkForm);
document.getElementById('email').addEventListener('input', checkForm);
document.getElementById('password').addEventListener('input', checkForm);
document.getElementById('phone_number').addEventListener('input', checkForm);
</script>

@endsection