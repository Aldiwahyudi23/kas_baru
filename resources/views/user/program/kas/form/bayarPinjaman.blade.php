                <form action="{{ route('bayar-pinjaman.store') }}" method="POST" enctype="multipart/form-data"
                    id="adminForm">
                    @csrf

                    <!-- Jumlah Pembayaran -->
                    <div class="form-group">
                        <label for="amount">Jumlah Pembayaran <span class="text-danger">*</span></label>
                        <input type="text" name="amount_display" id="amount_display"
                            value="{{ old('amount') ? number_format(old('amount'), 2, ',', '.') : '' }}"
                            class="form-control col-12 @error('amount') is-invalid @enderror"
                            placeholder="Masukkan nominal yang diajukan" oninput="formatIndonesian(this)">
                        <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="form-group">
                        <label for="payment_method">Metode Pembayaran</label>
                        <span class="text-danger">*</span></label>
                        <select class="select2bs4 @error('payment_method') is-invalid @enderror" style="width: 100%;"
                            name="payment_method" id="payment_method" onchange="toggleTransferReceipt()">
                            <option value="">--Pilih Pembayaran--</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>
                                Transfer</option>
                        </select>
                    </div>

                    <!-- Upload Bukti Transfer (jika metode transfer) -->
                    <div class="form-group" id="transfer_receipt"
                        style="display: {{ old('payment_method') == 'transfer' ? 'block' : 'none' }};">
                        <label for="transfer_receipt_path">Upload Bukti Transfer</label>
                        <span class="text-danger">*</span></label>
                        <input type="file" name="transfer_receipt_path" id="transfer_receipt_path" accept="image/*"
                            class="form-control col-12 @error('transfer_receipt_path') is-invalid @enderror"
                            onchange="preview('.tampil-gambar', this.files[0])">

                        <div class="tampil-gambar mt-3"></div>
                        @error('transfer_receipt_path')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-sm-12 col-form-label">Keterangan
                            <span class="text-danger">*</span></label>
                        <textarea class="form-control col-12 @error('description') is-invalid @enderror"
                            name="description" id="description">{{ old('description') }}</textarea>
                    </div>
                    <input type="hidden" name="data_warga_id" value="{{$pinjaman->data_warga_id}}">
                    <!-- Menagmbil data Loan id -->
                    <input type="hidden" name="loan_id" value="{{$pinjaman->id}}">
                    <!-- Button Submit -->
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-success" id="submitBtns">Bayar Pinjaman</button>

                        @if ($cek_pinjaman_2)
                        <!-- Jika ada data pinjaman di data extension maka kosongkan  -->
                        @else
                        @if ( $hitungWaktu <= 7 ) <a
                            href="{{ route('pinjaman-ke-dua.pengajuan', Crypt::encrypt($pinjaman->id)) }}"
                            class="btn btn-warning">Pinjaman ke 2</a>
                            @endif
                            @endif
                    </div>

                </form>
                <br>
                <!-- /.card-body -->
                <div class="card-footer">
                    Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                        <br>- Bertanda bintang Merah wajib di isi.
                    </p>
                </div>

                @section('script')

                <script>
                    // Function to toggle the visibility of the transfer receipt input based on selected payment method
                    function toggleTransferReceipt() {
                        var paymentMethod = document.getElementById('payment_method').value;
                        var transferReceipt = document.getElementById('transfer_receipt');
                        transferReceipt.style.display = (paymentMethod === 'transfer') ? 'block' : 'none';
                    }
                </script>
                @endsection