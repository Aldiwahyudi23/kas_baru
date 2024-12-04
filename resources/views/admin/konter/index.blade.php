@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12 col-md-6">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default {{ $errors->any() ? '' : 'collapsed-card' }}">
            <div class="card-header">
                <h3 class="card-title">Data Kategori</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @livewire('konter.kategori-konter-crud')
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                    <br>- Sesuaikan data dengan kebutuhan.
                </p>
            </div>
        </div>
        <!-- /.card -->
    </div>
    <div class="col-12 col-md-6">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default {{ $errors->any() ? '' : 'collapsed-card' }}">
            <div class="card-header">
                <h3 class="card-title">Data Provider</h3>

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
                @livewire('konter.provider-konter-crud')
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
        <div class="card card-default collapsed-card}}">
            <div class="card-header">
                <h3 class="card-title">Data Product Jual</h3>

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
                <!-- Data ini di ambil dari file terpisah view/admin/master_data/data_admin/tabel -->
                @livewire('konter.product-konter-crud')
                <!-- /.card -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                    <br>- Admin hanya bisa ditambahkan max 3.
                </p>
            </div>
        </div>
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
        const phoneNumber = document.getElementById('description').value.trim();

        // Check if all inputs are filled and if password >= 6 and description >= 10
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
    document.getElementById('description').addEventListener('input', checkForm);
</script>

@endsection