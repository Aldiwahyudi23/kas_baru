            <div class="card-body">
                <form action="{{ route('access-notification.store') }}" method="POST" enctype="multipart/form-data"
                    id="adminForm">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{old('name')}}"
                                    class="form-control col-12 @error('name') is-invalid @enderror">
                            </div>
                            <div class="form-group">
                                <label for="type">Icon <span class="text-danger">*</span></label>
                                <input type="text" name="type" id="type" value="{{old('type')}}"
                                    class="form-control col-12 @error('type') is-invalid @enderror">
                            </div>
                            <div class="form-group">
                                <label for="keterangan" class="col-sm-2 col-form-label">Deskripsi <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <textarea
                                        class="summernote-textarea form-control col-12 @error('keterangan') is-invalid @enderror"
                                        name="keterangan" id="keterangan">{{ old('keterangan') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="wa" id="wa" value="1"
                                        {{ old('wa') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="wa">Whatsapp</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="email" id="email"
                                        value="1" {{ old('email') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="email">Email</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="anggota" id="anggota"
                                        value="1" {{ old('anggota') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="anggota">Anggota</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" name="program" id="program"
                                        value="1" {{ old('program') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="program">Program</label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <!-- Checkbox Pengurus -->
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="pengurus" name="pengurus"
                                        onchange="toggleRoles()" value="1" {{ old('pengurus') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="pengurus">Pengurus</label>
                                </div>
                                <small class="text-muted">
                                    Jika Pengurus di Aktifkan maka pilih pengurus yang akan menerima Notif
                                </small>
                            </div>

                            <!-- Roles Container -->

                            <div class="row" id="rolesContainer" style="display: none;">
                                <!-- Foreach Menampilkan Data Access Notification -->
                                @foreach ($roles as $role)
                                <div class="col-4 col-sm-4">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox"
                                                id="customCheckbox{{ $role->id }}" name="role_id">
                                            <label for="customCheckbox{{ $role->id }}" class="custom-control-label">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <script>
                        function toggleRoles() {
                            const pengurusCheckbox = document.getElementById('pengurus');
                            const rolesContainer = document.getElementById('rolesContainer');

                            // Tampilkan atau sembunyikan rolesContainer berdasarkan checkbox pengurus
                            if (pengurusCheckbox.checked) {
                                rolesContainer.style.display = 'flex'; // Tampilkan
                            } else {
                                rolesContainer.style.display = 'none'; // Sembunyikan
                            }
                        }

                        // Inisialisasi pada load pertama (jaga-jaga jika checkbox sudah dicentang dari awal)
                        document.addEventListener('DOMContentLoaded', () => {
                            toggleRoles();
                        });
                        </script>

                    </div>
                    <br>
                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Create</button>
                </form>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                    <br>- Bertanda bintang merah wajib di isi.
                </p>
            </div>