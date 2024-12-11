<!-- Kode ini untuk isi tabel di dalam index data_admin -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Pembayaran KAS </h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        {{-- Tabel Loan Repayments --}}
        <table class="table table-bordered table-resposive datatable1 ">
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
        <p><strong>Total Kas Dipilih:</strong> Rp<span id="totalKas">0</span></p>
    </div>
</div>