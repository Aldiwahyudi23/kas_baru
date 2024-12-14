                <form action="{{ route('kas.store') }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @csrf
                    <p class="btn btn-primary toggle-text" onclick="toggleInput()">Input Kas Anggota</p>
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
                        <p class="label-info">Jika bayar kas untuk sendiri, kosongkan.</p>
                    </div>
                    @endif
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
                        @error('transfer_receipt_path')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="tampil-gambar mt-3"></div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-sm-12 col-form-label">Keterangan
                            <span class="text-danger">*</span></label>
                        <textarea class="form-control col-12 @error('description') is-invalid @enderror"
                            name="description" id="description">{{ old('description') }}</textarea>
                    </div>
                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Bayar Kas</button>
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