@extends('user.layout.app')

@section('content')

<!-- ./row -->
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-four-messages-tab" data-toggle="pill"
                            href="#custom-tabs-four-messages" role="tab" aria-controls="custom-tabs-four-messages"
                            aria-selected="false">Konter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " id="custom-tabs-four-home-tab" data-toggle="pill"
                            href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home"
                            aria-selected="true">Kas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill"
                            href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile"
                            aria-selected="false">Bayar Pinjaman</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-four-income-tab" data-toggle="pill"
                            href="#custom-tabs-four-income" role="tab" aria-controls="custom-tabs-four-income"
                            aria-selected="false">Income</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <form id="adminForm" method="POST" action="{{ route('setor-tunai.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <div class="tab-pane fade " id="custom-tabs-four-home" role="tabpanel"
                            aria-labelledby="custom-tabs-four-home-tab">
                            <!-- Mengambil data tabel  -->
                            @include('user.setor_tunai.tabel.kas')
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel"
                            aria-labelledby="custom-tabs-four-profile-tab">
                            <!-- Mengambil data tabel  -->
                            @include('user.setor_tunai.tabel.bayarPinjaman')
                        </div>
                        <div class="tab-pane fade show active" id="custom-tabs-four-messages" role="tabpanel"
                            aria-labelledby="custom-tabs-four-messages-tab">
                            <!-- Mengambil data tabel  -->
                            @include('user.setor_tunai.tabel.konter')
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-four-income" role="tabpanel"
                            aria-labelledby="custom-tabs-four-income-tab">
                            <!-- Mengambil data tabel  -->
                            @include('user.setor_tunai.tabel.income')
                        </div>
                    </div>
                    {{-- Hidden Input untuk Total Kas --}}
                    <p>Total Kas Dipilih : Rp <span id="totalKas1">0</span></p>
                    <input type="hidden" id="totalKasInput" name="total_kas">
                    {{-- Hidden Input untuk Total Pinjaman --}}
                    <p>Total Pinjaman Dipilih : Rp <span id="totalLoan1">0</span></p>
                    <input type="hidden" id="totalLoanInput" name="total_loan">
                    {{-- Hidden Input untuk Total Pinjaman --}}
                    <p>Total Konter Dipilih : Rp <span id="totalKonter1">0</span></p>
                    <input type="hidden" id="totalKonterInput" name="total_konter">
                    {{-- Hidden Input untuk Total Pinjaman --}}
                    <p>Total Income Dipilih : Rp <span id="totalIncome1">0</span></p>
                    <input type="hidden" id="totalIncomeInput" name="total_income">

                    {{-- Total Semua --}}
                    <div class="alert alert-success alert-dismissible">
                        <center>
                            <h3>Total Keseluruhan <br> <strong>Rp <span id="totalKeseluruhan">0</span> </strong></h3>
                        </center>
                    </div>

                    <input type="hidden" id="totalKeseluruhanInput" name="total_all">

                    {{-- Input Tambahan --}}
                    <!-- Upload Bukti Transfer (jika metode transfer) -->
                    <div class="form-group">
                        <label for="photo">Upload Bukti Transfer</label>
                        <span class="text-danger">*</span></label>
                        <input type="file" name="photo" id="photo" accept="image/*"
                            class="form-control col-12 @error('photo') is-invalid @enderror"
                            onchange="preview('.tampil-gambar', this.files[0])">
                        @error('photo')
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
                    <button type="submit" class="btn btn-success" id="submitBtns">Setor Tunai</button>
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>

    <div class="col-12 col-sm-6">
        <!-- Mengambil data tabel  -->
        @include('user.setor_tunai.tabel.data_deposit')
    </div>
</div>
<!-- /.row -->
@endsection

@section('script')
<script>
    // Update Total saat checkbox berubah
    function updateTotals() {
        let totalKas = 0;
        let totalLoan = 0;
        let totalKonter = 0;
        let totalIncome = 0;

        // Hitung total Kas
        document.querySelectorAll('.kasCheckbox:checked').forEach(el => {
            totalKas += parseFloat(el.dataset.amount);
        });

        // Hitung total Loan
        document.querySelectorAll('.loanCheckbox:checked').forEach(el => {
            totalLoan += parseFloat(el.dataset.amount);
        });

        // Hitung total Loan
        document.querySelectorAll('.konterCheckbox:checked').forEach(el => {
            totalKonter += parseFloat(el.dataset.amount);
        });
        // Hitung total Loan
        document.querySelectorAll('.incomeCheckbox:checked').forEach(el => {
            totalIncome += parseFloat(el.dataset.amount);
        });

        // Update total keseluruhan
        const totalKeseluruhan = totalKas + totalLoan + totalKonter + totalIncome;

        // Tampilkan total
        document.getElementById('totalKas').innerText = totalKas.toLocaleString();
        document.getElementById('totalLoan').innerText = totalLoan.toLocaleString();
        document.getElementById('totalKonter').innerText = totalKonter.toLocaleString();
        document.getElementById('totalIncome').innerText = totalIncome.toLocaleString();

        document.getElementById('totalKas1').innerText = totalKas.toLocaleString();
        document.getElementById('totalLoan1').innerText = totalLoan.toLocaleString();
        document.getElementById('totalKonter1').innerText = totalKonter.toLocaleString();
        document.getElementById('totalIncome1').innerText = totalIncome.toLocaleString();
        document.getElementById('totalKeseluruhan').innerText = totalKeseluruhan.toLocaleString();

        // Set hidden input values
        document.getElementById('totalKasInput').value = totalKas;
        document.getElementById('totalLoanInput').value = totalLoan;
        document.getElementById('totalKonterInput').value = totalKonter;
        document.getElementById('totalIncomeInput').value = totalIncome;
        document.getElementById('totalKeseluruhanInput').value = totalKeseluruhan;
    }

    // Event Listener untuk checkbox
    document.querySelectorAll('.kasCheckbox, .loanCheckbox, .konterCheckbox, .incomeCheckbox').forEach(el => {
        el.addEventListener('change', updateTotals);
    });

    // Select All Checkbox Logic
    document.getElementById('selectAllKas').addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.kasCheckbox').forEach(el => el.checked = isChecked);
        updateTotals();
    });

    document.getElementById('selectAllLoans').addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.loanCheckbox').forEach(el => el.checked = isChecked);
        updateTotals();
    });
    document.getElementById('selectAllKonter').addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.konterCheckbox').forEach(el => el.checked = isChecked);
        updateTotals();
    });
    document.getElementById('selectAllIncome').addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.incomeCheckbox').forEach(el => el.checked = isChecked);
        updateTotals();
    });
</script>
@endsection