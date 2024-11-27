@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default {{ $errors->any() ? '' : 'collapsed-card' }}">
            <div class="card-header">
                <h3 class="card-title">Tambah Data Warga</h3>

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
            @include('admin.master_data.data_warga.create')
        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.row -->
<div class="row">
    <div class="col-12">
        <!-- Data ini di ambil dari file terpisah view/admin/master_data/data_admin/tabel -->
        @include('admin.master_data.data_warga.tabel')
        <!-- /.card -->
    </div>
</div>
@endsection

@section('script')

<script>
    $(document).ready(function() {
        // Function untuk mengatur input pasangan berdasarkan status pernikahan
        function updatePasanganOptions(jenisKelamin) {
            var selectedStatus = $('#status_pernikahan').val();
            var pasanganSelect = $('#pasangan');

            // Kosongkan opsi pasangan saat jenis kelamin berubah
            pasanganSelect.empty().append('<option value="">--Pilih Pasangan--</option>');

            // Tampilkan data pasangan sesuai jenis kelamin dan status pernikahan
            if (jenisKelamin && selectedStatus !== 'Belum Menikah') {
                $.ajax({
                    url: "{{route('getPasangan')}}", // Sesuaikan endpoint untuk filter data pasangan
                    type: 'GET',
                    data: {
                        jenis_kelamin: jenisKelamin
                    },
                    success: function(data) {
                        $('#pasangan').empty().append('<option value="">Pilih Pasangan</option>');

                        if (Object.keys(data).length > 0) {
                            $.each(data, function(id, name) {
                                $('#pasangan').append(`<option value="${id}">${name}</option>`);
                            });
                        } else {
                            $('#pasangan').append('<option value="">Tidak ada pasangan tersedia</option>');
                        }
                    },
                    error: function() {
                        alert('Gagal mengambil data pasangan');
                    }
                });
                $('#pasanganGroup').show();
            } else {
                $('#pasanganGroup').hide();
                pasanganSelect.append('<option value="Belum Menikah" selected>Belum Menikah</option>');
            }
        }

        // Event listener untuk perubahan jenis kelamin
        $('#jenis_kelamin').change(function() {
            var jenisKelamin = $(this).val();
            updatePasanganOptions(jenisKelamin);
        });

        // Event listener untuk perubahan status pernikahan
        $('#status_pernikahan').change(function() {
            var jenisKelamin = $('#jenis_kelamin').val();
            updatePasanganOptions(jenisKelamin);

            // Tampilkan atau sembunyikan tanggal pernikahan
            if ($(this).val() !== 'Belum Menikah') {
                $('#tanggalPernikahanGroup').show();
            } else {
                $('#tanggalPernikahanGroup').hide();
                $('#tanggal_pernikahan').val(''); // Kosongkan tanggal pernikahan
            }
        });

        // Set tampilan awal berdasarkan nilai old
        updatePasanganOptions($('#jenis_kelamin').val());
    });
</script>
@endsection