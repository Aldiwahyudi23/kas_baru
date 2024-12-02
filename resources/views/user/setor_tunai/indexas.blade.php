@extends('user.layout.app')

@section('content')
<div class="container">
    <h2>Halaman Setor Tunai</h2>
    <form id="setorTunaiForm" method="POST" action="{{ route('setor-tunai.store') }}">
        @csrf

        {{-- Tabel Kas Payments --}}
        <h3>Data Kas</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllKas"></th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Nominal</th>
                    <th>Tanggal Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kasPayments as $kas)
                <tr>
                    <td>
                        <input type="checkbox" class="kasCheckbox" name="selected_ids[]" value="kas-{{ $kas->id }}"
                            data-amount="{{ $kas->amount }}">
                    </td>
                    <td>{{ $kas->code }}</td>
                    <td>{{ $kas->data_warga->name }}</td>
                    <td>Rp{{ number_format($kas->amount, 0, ',', '.') }}</td>
                    <td>{{ $kas->payment_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p><strong>Total Kas Dipilih:</strong> <span id="totalKas">0</span></p>

        {{-- Tabel Loan Repayments --}}
        <h3>Data Pinjaman</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllLoan"></th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Nominal</th>
                    <th>Tanggal Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($loanRepayments as $loan)
                <tr>
                    <td>
                        <input type="checkbox" class="loanCheckbox" name="selected_ids[]" value="loan-{{ $loan->id }}"
                            data-amount="{{ $loan->amount }}">
                    </td>
                    <td>{{ $loan->code }}</td>
                    <td>{{ $loan->data_warga->name }}</td>
                    <td>{{ number_format($loan->amount, 0, ',', '.') }}</td>
                    <td>{{ $loan->payment_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p><strong>Total Pinjaman Dipilih:</strong> <span id="totalLoan">0</span></p>

        {{-- Tabel Konter Payments --}}
        <h3>Data Konter</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllKonter"></th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Nominal</th>
                    <th>Tanggal Pembayaran</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
        <p><strong>Total Konter Dipilih:</strong> <span id="totalKonter">0</span></p>

        {{-- Tabel Tabungan Payments --}}
        <h3>Data Tabungan</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllTabungan"></th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Nominal</th>
                    <th>Tanggal Pembayaran</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
        <p><strong>Total Tabungan Dipilih:</strong> <span id="totalTabungan">0</span></p>

        {{-- Total Semua --}}
        <p><strong>Total Keseluruhan:</strong> <span id="totalKeseluruhan">0</span></p>

        {{-- Input Tambahan --}}
        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" class="form-control" rows="4"
                placeholder="Tambahkan deskripsi..."></textarea>
        </div>

        <div class="form-group">
            <label for="photo">Unggah Foto</label>
            <input type="file" id="photo" name="photo" class="form-control-file">
        </div>

        <button type="submit" class="btn btn-primary">Setor Tunai</button>
    </form>
</div>

<script>
    function formatRupiah(number) {
        return number.toLocaleString('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).replace('IDR', '').trim();
    }

    function updateTotals(className, totalId) {
        let total = 0;
        document.querySelectorAll(className + ':checked').forEach(cb => {
            total += parseFloat(cb.getAttribute('data-amount'));
        });
        document.getElementById(totalId).textContent = formatRupiah(total);
        updateTotalKeseluruhan();
    }

    function updateTotalKeseluruhan() {
        const totals = ['totalKas', 'totalLoan', 'totalKonter', 'totalTabungan'].map(id =>
            parseFloat(document.getElementById(id).textContent.replace(/\./g, '').replace(/,/g, '') || 0)
        );
        const grandTotal = totals.reduce((acc, curr) => acc + curr, 0);
        document.getElementById('totalKeseluruhan').textContent = formatRupiah(grandTotal);
    }

    document.getElementById('selectAllKas').addEventListener('change', function() {
        document.querySelectorAll('.kasCheckbox').forEach(cb => cb.checked = this.checked);
        updateTotals('.kasCheckbox', 'totalKas');
    });

    document.querySelectorAll('.kasCheckbox').forEach(cb => cb.addEventListener('change', () => updateTotals('.kasCheckbox',
        'totalKas')));

    document.getElementById('selectAllLoan').addEventListener('change', function() {
        document.querySelectorAll('.loanCheckbox').forEach(cb => cb.checked = this.checked);
        updateTotals('.loanCheckbox', 'totalLoan');
    });

    document.querySelectorAll('.loanCheckbox').forEach(cb => cb.addEventListener('change', () => updateTotals(
        '.loanCheckbox', 'totalLoan')));

    document.getElementById('selectAllKonter').addEventListener('change', function() {
        document.querySelectorAll('.konterCheckbox').forEach(cb => cb.checked = this.checked);
        updateTotals('.konterCheckbox', 'totalKonter');
    });

    document.querySelectorAll('.konterCheckbox').forEach(cb => cb.addEventListener('change', () => updateTotals(
        '.konterCheckbox', 'totalKonter')));

    document.getElementById('selectAllTabungan').addEventListener('change', function() {
        document.querySelectorAll('.tabunganCheckbox').forEach(cb => cb.checked = this.checked);
        updateTotals('.tabunganCheckbox', 'totalTabungan');
    });

    document.querySelectorAll('.tabunganCheckbox').forEach(cb => cb.addEventListener('change', () => updateTotals(
        '.tabunganCheckbox', 'totalTabungan')));
</script>
@endsection