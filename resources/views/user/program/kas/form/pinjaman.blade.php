                <form action="{{ route('pinjaman.store') }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @csrf
                    <p class="btn btn-primary toggle-text" onclick="toggleInput()">Input Pinjaman Anggota</p>
                    <!-- Hanya pengurus yang bisa -->
                    @if(Auth::user()->role->name == "Bendahara" || Auth::user()->role->name == "Wakil Bendahara" ||
                    Auth::user()->role->name == "Sekretaris" || Auth::user()->role->name == "Wakil Sekretaris" ||
                    Auth::user()->role->name == "Ketua" || Auth::user()->role->name == "Wakil Ketua")
                    <!-- Metode Pembayaran -->
                    <div id="inputForm" class="form-group hidden">
                        <label for="data_warga_id">Pilih Anggota</label>
                        <span class="text-danger">*</span></label>
                        <select class="select2bs4 @error('data_warga_id') is-invalid @enderror" style="width: 100%;"
                            name="data_warga_id" id="data_warga_id">
                            <option value="">--Pilih Anggota--</option>
                            @foreach ($access as $data )
                            <option value="{{$data->data_warga_id}}"
                                {{ old('data_warga_id') == $data->data_warga_id ? 'selected' : '' }} @if($data->
                                is_active == 0) disabled @endif>
                                {{$data->dataWarga->name}}
                                @if($data->is_active == 0) (Tidak Aktif) @endif
                            </option>
                            @endforeach
                        </select>
                        <p class="label-info">Jika pengajuan Pinjaman untuk sendiri, kosongkan.</p>
                    </div>
                    @endif
                    <!-- Jumlah Pembayaran -->
                    <div class="form-group">
                        <label for="amount">Jumlah Pinjaman <span class="text-danger">*</span></label>
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
                    <!-- Metode Pengambilan -->
                    <div class="form-group">
                        <label for="payment_method">Metode Pengambilan</label>
                        <span class="text-danger">*</span>
                        <select class="select2bs4 @error('payment_method') is-invalid @enderror" style="width: 100%;"
                            name="payment_method" id="payment_method" onchange="togglePaymentFields()">
                            <option value="">--Pilih Pengambilan--</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>
                                Transfer</option>
                        </select>
                    </div>
                    <!-- Input untuk Transfer -->
                    <div id="transfer_fields"
                        style="display: {{ old('payment_method') == 'transfer' ? 'block' : 'none' }};">
                        <div class="form-group">
                            <label for="bank_name">Nama Bank / E-Wallet</label>
                            <span class="text-danger">*</span>
                            <input type="text" name="bank_name" id="bank_name"
                                class="form-control @error('bank_name') is-invalid @enderror"
                                value="{{ old('bank_name') }}" placeholder="Masukkan nama bank">
                        </div>

                        <div class="form-group">
                            <label for="account_name">Atas Nama</label>
                            <span class="text-danger">*</span>
                            <input type="text" name="account_name" id="account_name"
                                class="form-control @error('account_name') is-invalid @enderror"
                                value="{{ old('account_name') }}" placeholder="Masukkan nama pemilik rekening">
                        </div>

                        <div class="form-group">
                            <label for="account_number">No Rekening / E-Wallet</label>
                            <span class="text-danger">*</span>
                            <input type="number" name="account_number" id="account_number"
                                class="form-control @error('account_number') is-invalid @enderror"
                                value="{{ old('account_number') }}" placeholder="Masukkan nomor rekening atau e-wallet">
                        </div>
                    </div>

                    <!-- Input untuk Cash -->
                    <div id="cash_fields" style="display: {{ old('payment_method') == 'cash' ? 'block' : 'none' }};">
                        <div class="form-group">
                            <label for="cash_notes">Keterangan Pengambilan Cash</label>
                            <span class="text-danger">*</span>
                            <textarea name="cash_notes" id="cash_notes" rows="3"
                                class="form-control @error('cash_notes') is-invalid @enderror"
                                placeholder="Tambahkan catatan pengambilan cash jika diperlukan">{{ old('cash_notes') }}</textarea>
                            @error('cash_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <span class="text-muted">Jelaskan jika pengambilan cash, mau di ambil di mana.</span>
                        </div>
                    </div>

                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Pinjam Kas</button>
                </form>
                <br>
                <!-- /.card-body -->
                <div class="card-footer">
                    Catatan: <p>-Masukan nominal sesuai dengan kesepakatan.
                        <br>- Masukan no Bank / E-wallet dengan benar.
                        <br>- Tunggu 1x24 jam karena proses menyesuaikan dengan kesibukan pengurus.
                    </p>
                </div>

                @section('script')

                <script>
                    function togglePaymentFields() {
                        const paymentMethod = document.getElementById('payment_method').value;
                        const transferFields = document.getElementById('transfer_fields');
                        const cashFields = document.getElementById('cash_fields');

                        // Input elemen untuk Transfer
                        const bankName = document.getElementById('bank_name');
                        const accountName = document.getElementById('account_name');
                        const accountNumber = document.getElementById('account_number');
                        const transferReceiptPath = document.getElementById('transfer_receipt_path');

                        // Input elemen untuk Cash
                        const cashNotes = document.getElementById('cash_notes');

                        if (paymentMethod === 'transfer') {
                            transferFields.style.display = 'block';
                            cashFields.style.display = 'none';

                            // Menjadikan elemen transfer wajib diisi
                            bankName.setAttribute('required', 'required');
                            accountName.setAttribute('required', 'required');
                            accountNumber.setAttribute('required', 'required');
                            transferReceiptPath.setAttribute('required', 'required');

                            // Nonaktifkan elemen cash
                            cashNotes.removeAttribute('required');
                        } else if (paymentMethod === 'cash') {
                            cashFields.style.display = 'block';
                            transferFields.style.display = 'none';

                            // Menjadikan elemen cash wajib diisi
                            cashNotes.setAttribute('required', 'required');

                            // Nonaktifkan elemen transfer
                            bankName.removeAttribute('required');
                            accountName.removeAttribute('required');
                            accountNumber.removeAttribute('required');
                            transferReceiptPath.removeAttribute('required');
                        } else {
                            transferFields.style.display = 'none';
                            cashFields.style.display = 'none';

                            // Nonaktifkan semua elemen
                            bankName.removeAttribute('required');
                            accountName.removeAttribute('required');
                            accountNumber.removeAttribute('required');
                            transferReceiptPath.removeAttribute('required');
                            cashNotes.removeAttribute('required');
                        }
                    }

                    // Initialize fields on page load based on old value (if any)
                    document.addEventListener('DOMContentLoaded', togglePaymentFields);
                </script>
                <script>
                    function toggleInput() {
                        const inputForm = document.getElementById('inputForm');
                        inputForm.classList.toggle('hidden');
                    }
                </script>
                @endsection

                @section(section: 'style')
                <style>
                    .hidden {
                        display: none;
                    }

                    .label-info {
                        font-size: 0.9em;
                        color: gray;
                        margin-top: 5px;
                    }

                    .toggle-text {
                        color: #007BFF;
                        cursor: pointer;
                        text-decoration: underline;
                    }

                    .toggle-text:hover {
                        color: #0056b3;
                    }
                </style>
                @endsection