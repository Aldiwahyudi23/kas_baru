            <div class="card-body">
                <form action="{{ route('warga.store') }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="name">Nama Warga <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{old('name')}}" class="form-control col-12 @error('name') is-invalid @enderror">
                            </div>

                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{old('tempat_lahir')}}" class="form-control col-12 @error('tempat_lahir') is-invalid @enderror">
                            </div>
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{old('tanggal_lahir')}}" class="form-control col-12 @error('tanggal_lahir') is-invalid @enderror">
                            </div>

                            <!-- Input Jenis Kelamin -->
                            <div class="form-group">
                                <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4 @error('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin">
                                    <option value="">--Pilih Jenis Kelamin--</option>
                                    <option value="Laki-Laki" {{ old('jenis_kelamin') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="agama">Agama <span class="text-danger">*</span></label>
                                <select class="form-control select2bs4 col-12 @error('agama') is-invalid @enderror" name="agama">
                                    @if(old('agama') == true)
                                    <option selected="selected" value=" {{old('agama')}}">{{old('agama')}}</option>
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
                                    <textarea class="form-control col-sm-12 @error('alamat') is-invalid @enderror" name="alamat" id="alamat">{{ old('alamat') }}</textarea>
                                </div>
                            </div>

                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="no_hp">No Handphone /Whatsapp</label>
                                <input type="text" name="no_hp" id="no_hp" value="{{old('no_hp')}}" class="form-control col-12 @error('no_hp') is-invalid @enderror">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" value="{{old('email')}}" class="form-control col-12 @error('email') is-invalid @enderror">
                            </div>
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

                            <div class="form-group">
                                <label for="foto">Upload Foto</label>
                                <input type="file" name="foto" id="foto" value="{{old('foto')}}" class="form-control col-12 @error('foto') is-invalid @enderror"
                                    onchange="preview('.tampil-gambar', this.files[0])">
                                <span class="help-block with-errors"></span>
                                <br>
                                <div class="tampil-gambar"></div>
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
                    <br>- Bertanda bintang merah artinya wajib di isi.
                    <br>- Untuk Tanggal setelah memilih menikah isi tanggal pernikahan ataupun yang lainnya
                </p>
            </div>

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

            <!-- jQuery Script -->


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