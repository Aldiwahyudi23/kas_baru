                <form action="{{ route('pengeluaran.store') }}" method="POST" enctype="multipart/form-data"
                    id="adminForm">
                    @csrf
                    <!-- Pilih Warga -->

                    <!-- Komponen Dropdown -->
                    @livewire('pengeluaran.pengeluaran-anggaran')

                    <!-- Jumlah Anggaran -->
                    <div class="form-group">
                        <label for="amount">Jumlah Anggaran <span class="text-danger">*</span></label>
                        <input type="text" name="amount_display" id="amount_display"
                            value="{{ old('amount') ? number_format(old('amount'), 2, ',', '.') : '' }}"
                            class="form-control col-12 @error('amount') is-invalid @enderror"
                            placeholder="Masukkan nominal yang diajukan" oninput="formatIndonesian(this)">
                        <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-sm-12 col-form-label">Keterangan
                            <span class="text-danger">*</span></label>
                        <textarea
                            class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror"
                            name="description" id="description">{{ old('description') }}</textarea>
                    </div>
                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Ajukan Pengeluaran</button>
                </form>
                <br>
                <!-- /.card-body -->
                <div class="card-footer">
                    Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                        <br>- Bertanda bintang Merah wajib di isi.
                        <br>-
                    </p>
                    <i>Pengeluaran ini sangat penting dan resiko sangat besar, maka dari itu harap input dengan
                        benar dan di keterangan jelaskan secara detail.</i>
                </div>