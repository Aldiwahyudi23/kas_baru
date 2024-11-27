            <div class="card-body">
                <form action="{{ route('loan.store') }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6">

                            <!-- Pilih Warga -->
                            <div class="form-group">
                                <label for="submitted_by">Di Input<span class="text-danger">*</span></label>
                                <select class="select2bs4 @error('submitted_by') is-invalid @enderror" style="width: 100%;" name="submitted_by" id="submitted_by">
                                    <option value="">--Pilih Pengaju--</option>
                                    @foreach ($user as $data)
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
                                <label for="data_warga_id">Data Warga <span class="text-danger">*</span></label>
                                <select class="select2bs4 @error('data_warga_id') is-invalid @enderror" style="width: 100%;" name="data_warga_id" id="data_warga_id">
                                    <option value="">--Pilih Warga--</option>
                                    @foreach ($data_warga as $data)
                                    <option value="{{$data->id}}" {{ old('data_warga_id') == $data->id ? 'selected' : ''}}>
                                        {{ $data->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Jumlah Pembayaran -->
                            <div class="form-group">
                                <label for="loan_amount">Jumlah Yang di Pinjam <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="loan_amount" id="loan_amount" value="{{old('loan_amount')}}" class="form-control col-12 @error('loan_amount') is-invalid @enderror">
                            </div>
                            <!-- Jumlah Pembayaran -->
                            <div class="form-group">
                                <label for="remaining_balance">Sisa <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="remaining_balance" id="remaining_balance" value="{{old('remaining_balance')}}" class="form-control col-12 @error('remaining_balance') is-invalid @enderror">
                            </div>
                            <!-- Jumlah Pembayaran -->
                            <div class="form-group">
                                <label for="overpayment_balance">Lebih<span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="overpayment_balance" id="overpayment_balance" value="{{old('overpayment_balance')}}" class="form-control col-12 @error('overpayment_balance') is-invalid @enderror">
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
                                    <option value="Acknowledged" {{ old('status') == 'Acknowledged' ? 'selected' : '' }}>Di Akui Warga</option>
                                    <option value="In Repayment" {{ old('status') == 'In Repayment' ? 'selected' : '' }}>Proses Cicil</option>
                                    <option value="Paid in Full" {{ old('status') == 'Paid in Full' ? 'selected' : '' }}>Selesai</option>
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
                                <label for="disbursement_receipt_path">Upload Bukti Transfer</label>
                                <input type="file" name="disbursement_receipt_path" id="disbursement_receipt_path" accept="image/*" class="form-control col-12 @error('disbursement_receipt_path') is-invalid @enderror">
                                @error('disbursement_receipt_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="disbursed_date">Tanggal Pencairan Oleh Bendahara <span class="text-danger">*</span></label>
                                <input type="date" name="disbursed_date" id="disbursed_date" value="{{old('disbursed_date')}}" class="form-control col-12 @error('disbursed_date') is-invalid @enderror">
                            </div>

                            <!-- Input untuk anggaran id -->
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