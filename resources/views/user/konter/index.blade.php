@extends('user.layout.app')

@section('content')

<!-- ./row -->
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill"
                            href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home"
                            aria-selected="true">Tagihan Aktif</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill"
                            href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile"
                            aria-selected="false">Pengajuan
                            @if ($pengajuan_proses->count() >= 1)
                            <span class="badge badge-warning">{{$pengajuan_proses->count()}}</span>
                            @endif
                        </a>
                    </li>

                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-four-tabContent">
                    <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel"
                        aria-labelledby="custom-tabs-four-home-tab">
                        <!-- Mengambil data tabel  -->
                        @include('user.konter.tabel.tagihan_aktif')
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel"
                        aria-labelledby="custom-tabs-four-profile-tab">
                        <!-- Mengambil data tabel  -->
                        @include('user.konter.tabel.pengajuan')
                    </div>
                </div>

            </div>
            <!-- /.card -->
        </div>
    </div>

    <div class="col-12 col-sm-6">
        <!-- Mengambil data tabel  -->
        @include('user.konter.tabel.data_sukses')
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

        // Hitung total Kas
        document.querySelectorAll('.kasCheckbox:checked').forEach(el => {
            totalKas += parseFloat(el.dataset.amount);
        });

        // Hitung total Loan
        document.querySelectorAll('.loanCheckbox:checked').forEach(el => {
            totalLoan += parseFloat(el.dataset.amount);
        });

        // Update total keseluruhan
        const totalKeseluruhan = totalKas + totalLoan;

        // Tampilkan total
        document.getElementById('totalKas').innerText = totalKas.toLocaleString();
        document.getElementById('totalLoan').innerText = totalLoan.toLocaleString();
        document.getElementById('totalKeseluruhan').innerText = totalKeseluruhan.toLocaleString();

        // Set hidden input values
        document.getElementById('totalKasInput').value = totalKas;
        document.getElementById('totalLoanInput').value = totalLoan;
        document.getElementById('totalKeseluruhanInput').value = totalKeseluruhan;
    }

    // Event Listener untuk checkbox
    document.querySelectorAll('.kasCheckbox, .loanCheckbox').forEach(el => {
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
</script>
@endsection