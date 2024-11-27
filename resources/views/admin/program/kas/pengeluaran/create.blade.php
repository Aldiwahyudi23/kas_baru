            <div class="card-body">
                <form action="{{ route('expenditure.store') }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6">

                            <!-- Pilih Warga -->
                            <div class="form-group">
                                <label for="submitted_by">Di Input<span class="text-danger">*</span></label>
                                <select class="select2bs4 @error('submitted_by') is-invalid @enderror" style="width: 100%;" name="submitted_by" id="submitted_by">
                                    <option value="">--Pilih Pengurus--</option>
                                    @foreach ($pengurus_user as $data)
                                    <option value="{{$data->data_warga_id}}" {{ old('submitted_by') == $data->data_warga_id ? 'selected' : '' }}
                                        @if($data->is_active == 0) disabled @endif>
                                        {{ $data->name }}
                                        @if($data->is_active == 0) (Tidak Aktif) @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Pilih Warga -->
                            <div class="form-group">
                                <label for="anggaran_id">Data Anggaran <span class="text-danger">*</span></label>
                                <select class="select2bs4 @error('anggaran_id') is-invalid @enderror" style="width: 100%;" name="anggaran_id" id="anggaran_id">
                                    <option value="">--Pilih Anggaran--</option>
                                    @foreach ($anggaran as $data)
                                    <option value="{{$data->id}}" {{ old('anggaran_id') == $data->id ? 'selected' : ''}}
                                        @if($data->is_active == 0 || $data->name === "Dana Pinjam") disabled @endif>
                                        {{ $data->name }}
                                        @if($data->is_active == 0) (Access Program Tidak Aktif) @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Jumlah Pembayaran -->
                            <div class="form-group">
                                <label for="amount">Jumlah Yang di Keluarkan <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" id="amount" value="{{old('amount')}}" class="form-control col-12 @error('amount') is-invalid @enderror">
                            </div>

                            <div class="form-group">
                                <label for="description" class="col-sm-12 col-form-label">Keterangan<span class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <textarea class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror" name="description" id="description">{{ old('description') }}</textarea>
                                </div>
                            </div>

                        </div>
                        <div class="col-12 col-sm-6">
                            <!-- Pilih Warga -->

                            <div class="form-group">
                                <label for="status">Satus Transaksi</label>
                                <select class="select2bs4 @error('status') is-invalid @enderror" style="width: 100%;" name="status" id="status">
                                    <option value="">--Pilih status--</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }} disabled>Pending</option>
                                    <option value="approved_by_chairman" {{ old('status') == 'approved_by_chairman' ? 'selected' : '' }} disabled>Persetujuan Ketua</option>
                                    <option value="disbursed_by_treasurer" {{ old('status') == 'disbursed_by_treasurer' ? 'selected' : '' }} disabled>Proses Pencairan oleh Bendahara</option>
                                    <option value="Acknowledged" {{ old('status') == 'Acknowledged' ? 'selected' : '' }}>Di Akui</option>
                                </select>
                            </div>
                            <!-- Pilih Warga -->
                            <div class="form-group">
                                <label for="approved_by">Persetujuan Ketua<span class="text-danger">*</span></label>
                                <select class="select2bs4 @error('approved_by') is-invalid @enderror" style="width: 100%;" name="approved_by" id="approved_by">
                                    <option value="">--Pilih Ketua--</option>
                                    @foreach ($pengurus_user as $data)
                                    <option value="{{$data->data_warga_id}}" {{ old('approved_by') == $data->data_warga_id ? 'selected' : ''}}
                                        @if($data->is_active == 0) disabled @endif>
                                        {{ $data->name }}
                                        @if($data->is_active == 0) (Tidak Aktif) @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="approved_date">Tanggal di Konfirmasi Oleh Ketua <span class="text-danger">*</span></label>
                                <input type="date" name="approved_date" id="approved_date" value="{{old('approved_date')}}" class="form-control col-12 @error('approved_date') is-invalid @enderror">
                            </div>

                            <!-- Pilih Warga -->
                            <div class="form-group">
                                <label for="disbursed_by">Input Pencairan<span class="text-danger">*</span></label>
                                <select class="select2bs4 @error('disbursed_by') is-invalid @enderror" style="width: 100%;" name="disbursed_by" id="disbursed_by">

                                    <option value="">--Pilih Bendahara--</option>
                                    @foreach ($pengurus_user as $data)
                                    <option value="{{$data->data_warga_id}}" {{ old('disbursed_by') == $data->data_warga_id ? 'selected' : ''}}
                                        @if($data->is_active == 0) disabled @endif>
                                        {{ $data->name }}
                                        @if($data->is_active == 0) (Tidak Aktif) @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Upload Bukti Transfer (jika metode transfer) -->
                            <div class="form-group" id="receipt">
                                <label for="receipt_path">Upload Bukti Transfer</label>
                                <input type="file" name="receipt_path" id="receipt_path" accept="image/*" class="form-control col-12 @error('receipt_path') is-invalid @enderror">
                                @error('receipt_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="disbursed_date">Tanggal Pencairan Oleh Bendahara <span class="text-danger">*</span></label>
                                <input type="date" name="disbursed_date" id="disbursed_date" value="{{old('disbursed_date')}}" class="form-control col-12 @error('disbursed_date') is-invalid @enderror">
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
                    <br>- Bertanda bintang Merah wajib di isi.
                    <br>- Untuk Keterangan Harap di tulis secara detail terkait pengeluaran, Nama atau kebutuhannya di jelaskan dan alasan di keluarkan
                </p>
            </div>

            @section('script')

            @endsection