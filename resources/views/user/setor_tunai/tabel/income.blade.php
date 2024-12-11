<!-- Kode ini untuk isi tabel di dalam index data_admin -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Pemasukan </h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        {{-- Tabel Loan Repayments --}}
        <table class="table table-bordered table-resposive datatable1 ">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllIncome"></th>
                    <th>Kode</th>
                    <th>Nama Anggaran</th>
                    <th>Nominal</th>
                    <th>Tanggal Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($incomes as $income)
                <tr>
                    <td>
                        <input type="checkbox" class="incomeCheckbox" name="selected_ids[]"
                            value="income-{{ $income->id }}" data-amount="{{ $income->amount }}">
                    </td>
                    <td>{{ $income->code }}</td>
                    <td>{{ $income->anggaran->name }}</td>
                    <td>Rp{{ number_format($income->amount, 0, ',', '.') }}</td>
                    <td>{{ $income->payment_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p><strong>Total Income Dipilih:</strong> Rp<span id="totalIncome">0</span></p>
    </div>
</div>