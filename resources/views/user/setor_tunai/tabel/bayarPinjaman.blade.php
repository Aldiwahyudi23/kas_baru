<!-- Kode ini untuk isi tabel di dalam index data_admin -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Pembayaran Pinjaman </h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body table-respnsive">
        {{-- Tabel Loan Repayments --}}
        <table class="table table-bordered  datatable1 ">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllLoans"></th>
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
                    <td>Rp{{ number_format($loan->amount, 0, ',', '.') }}</td>
                    <td>{{ $loan->payment_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p><strong>Total Pinjaman Dipilih:</strong> Rp<span id="totalLoan">0</span></p>
    </div>
</div>