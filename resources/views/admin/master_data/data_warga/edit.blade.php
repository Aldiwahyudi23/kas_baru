@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12">
        <!-- select2bs4 EXAMPLE -->
        <div class="card card-default ">
            <div class="card-header">
                <h3 class="card-title">Edit Data Warga</h3>

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
                <form action="{{ route('warga.update',Crypt::encrypt($dataWarga->id)) }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @method('PATCH')
                    {{csrf_field()}}

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="name">Nama Warga <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{old('name',$dataWarga->name)}}" class="form-control col-12 @error('name') is-invalid @enderror">
                            </div>

                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{old('tempat_lahir',$dataWarga->tempat_lahir)}}" class="form-control col-12 @error('tempat_lahir') is-invalid @enderror">
                            </div>
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir </label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{old('tanggal_lahir',$dataWarga->tanggal_lahir)}}" class="form-control col-12 @error('tanggal_lahir') is-invalid @enderror">
                                <span class="text-danger">Tanggal lahir {{$dataWarga->tanggal_lahir}} <br>Jika tidak mau di ubah, Kosongkan</span>
                            </div>

                            <!-- Input Jenis Kelamin -->
                            <div class="form-group">
                                <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4 @error('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin">
                                    <option value="">--Pilih Jenis Kelamin--</option>
                                    <option value="Laki-Laki" {{ old('jenis_kelamin',$dataWarga->jenis_kelamin) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin',$dataWarga->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <!-- mengirim data warga id ke controler -->
                            <input type="hidden" name="warga_id" value="{{$dataWarga->id}}">

                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="agama">Agama <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4 col-12 @error('agama') is-invalid @enderror" name="agama">
                                    @if(old('agama',$dataWarga->agama) == true)
                                    <option selected="selected" value=" {{old('agama',$dataWarga->agama)}}">{{old('agama',$dataWarga->agama)}}</option>
                                    @endif
                                    <option value="">--pilih Agama--</option>
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Khonghucu">Khonghucu</option>
                                </select>
                            </div>
                            <div class="form-group row">
                                <label for="alamat" class="col-sm-2 col-form-label">Alamat <span class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <textarea class="form-control col-sm-12 @error('alamat') is-invalid @enderror" name="alamat" id="alamat">{{ old('alamat',$dataWarga->alamat) }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="no_hp">No Handphone /Whatsapp</label>
                                <input type="text" name="no_hp" id="no_hp" value="{{old('no_hp',$dataWarga->no_hp)}}" class="form-control col-12 @error('no_hp') is-invalid @enderror">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" value="{{old('email',$dataWarga->email)}}" class="form-control col-12 @error('email') is-invalid @enderror">
                            </div>
                        </div>
                    </div>

                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Update Data</button>
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
        <!-- select2bs4 EXAMPLE -->
        <div class="card card-default ">
            <div class="card-header">
                <h3 class="card-title">Edit Data Pernikahan</h3>

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
                <form action="{{ route('update.pernikahan') }}" method="POST" enctype="multipart/form-data" id="adminForm1">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <!-- Input Status Pernikahan -->
                            <div class="form-group">
                                <label>Status Pernikahan <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4 @error('status_pernikahan') is-invalid @enderror" name="status_pernikahan" id="status_pernikahan">
                                    <option value="">--Pilih Status Pernikahan--</option>
                                    <option value="Belum Menikah" {{ old('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                    <option value="Menikah" {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                    <option value="Cerai Hidup" {{ old('status_pernikahan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                    <option value="Cerai Mati" {{ old('status_pernikahan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                                </select>
                            </div>

                            <!-- Select Pasangan, Hidden Awal -->
                            <div class="form-group" id="pasanganGroup" style="display: none;">
                                <label>Nama Pasangan <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4 @error('pasangan_id') is-invalid @enderror" name="pasangan_id" id="pasangan">
                                    <option value="">--Pilih Pasangan--</option>
                                </select>
                            </div>

                            <!-- Input Tanggal Pernikahan, Hidden Awal -->
                            <div class="form-group" id="tanggalPernikahanGroup" style="display: none;">
                                <label>Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" name="tanggal" id="tanggal_pernikahan" placeholder="Masukan tanggal" value="{{ old('tanggal_pernikahan') }}">
                            </div>
                            <!-- mengirim data warga id ke controler -->
                            <input type="hidden" name="warga_id" value="{{$dataWarga->id}}">
                            <input type="hidden" name="jenis_kelamin" value="{{$dataWarga->jenis_kelamin}}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <table id="example1" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Status</th>
                                        <th>Pasangan</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    use App\Models\DataWarga;
                                    use App\Models\User;

                                    $no = 0; ?>
                                    @foreach($pernikahan as $data)
                                    <?php $no++;
                                    ?>
                                    <tr>
                                        <td>{{$no}} </td>
                                        <td>{{$data->status}} </td>
                                        <td>
                                            @if($dataWarga->jenis_kelamin == "Perempuan")

                                            @if($data->warga_suami_id == NULL)

                                            @else
                                            <?php
                                            $warga = DataWarga::Find($data->warga_suami_id);
                                            ?>
                                            {{$warga->name}}
                                            @endif
                                            @endif
                                            @if($dataWarga->jenis_kelamin == "Laki-Laki")
                                            @if($data->warga_istri_id == NULL)

                                            @else
                                            <?php
                                            $warga = DataWarga::Find($data->warga_istri_id);
                                            ?>
                                            {{$warga->name}}
                                            @endif
                                            @endif
                                        </td>
                                        <td>{{$data->tanggal}} </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns1">Update Pernikahan</button>
                </form>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                Catatan: <p>- Nama Pasangan akan muncul ketika sudah memilih jenis kelamin dan Pernikahan
                </p>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
<div class="row">
    <div class="col-12">
        <!-- select2bs4 EXAMPLE -->
        <div class="card card-default ">
            <div class="card-header">
                <h3 class="card-title">Edit Data Pekerjaan</h3>

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
                <form action="{{ route('update.pekerjaan') }}" method="POST" enctype="multipart/form-data" id="adminForm2">
                    @csrf

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <!-- Input Status Pekerjaan -->
                            <div class="form-group">
                                <label>Apakah Aktif Pekerja <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4 @error('status') is-invalid @enderror" style="width: 100%;" name="status_pekerjaan" id="status">
                                    <option value="">--Pilih Status Pekerjaan--</option>
                                    <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Tidak Aktif" {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>

                            <!-- Textarea Pekerjaan -->
                            <div class="form-group row" id="pekerjaanGroup" style="display: none;">
                                <label for="pekerjaan" class="col-sm-2 col-form-label">Pekerjaan <span class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <textarea class="form-control col-sm-12 @error('pekerjaan') is-invalid @enderror" name="pekerjaan" id="pekerjaan" placeholder="Masukan pekerjaan, contoh: Pegawai Swasta">{{ old('status') == 'Aktif' ? old('pekerjaan') : 'Tidak Bekerja' }}</textarea>
                                </div>
                            </div>
                            <input type="hidden" name="warga_id" value="{{$dataWarga->id}}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <table id="example1" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Status</th>
                                        <th>Pekerjaan</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0; ?>
                                    @foreach($pekerjaan as $data)
                                    <?php $no++; ?>
                                    <tr>
                                        <td>{{$no}} </td>
                                        <td>{{$data->status}} </td>
                                        <td>
                                            {{$data->pekerjaan}}
                                        </td>
                                        <td>{{$data->created_at}} </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns2">Update Pekerjaan</button>
                </form>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                Catatan: <p>- Pilih Aktif jika sedang bekerja mencari uang, lalu catat pekerjaannya apa
                </p>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        function togglePekerjaanField() {
            var status = $('#status').val();
            if (status === 'Aktif') {
                $('#pekerjaanGroup').show(); // Menampilkan textarea
                $('#pekerjaan').val(''); // Kosongkan untuk diisi manual
            } else if (status === 'Tidak Aktif') {
                $('#pekerjaanGroup').hide(); // Sembunyikan textarea
                $('#pekerjaan').val('Tidak Bekerja'); // Isi otomatis
            } else {
                $('#pekerjaanGroup').hide(); // Sembunyikan jika tidak memilih apapun
                $('#pekerjaan').val('');
            }
        }

        // Jalankan saat halaman dimuat untuk mengatur tampilan awal sesuai dengan nilai lama
        togglePekerjaanField();

        // Jalankan fungsi setiap kali ada perubahan pada select
        $('#status').change(togglePekerjaanField);
    });
</script>


<script>
    // jQuery Script
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
                    url: "{{ route('getPasangan') }}", // Sesuaikan endpoint untuk filter data pasangan
                    type: 'GET',
                    data: {
                        jenis_kelamin: jenisKelamin
                    },
                    success: function(data) {
                        $('#pasangan').empty().append('<option value="">Pilih Pasangan</option>');

                        if (Object.keys(data).length > 0) {
                            $.each(data, function(index, warga) {
                                // Menampilkan nama dan status di dropdown
                                $('#pasangan').append(`<option value="${warga.id}">${warga.name} (${warga.status})</option>`);
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