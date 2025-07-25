<!-- Kode ini untuk isi tabel di dalam index data_admin -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data KONTER </h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body table-respnsive">
        {{-- Tabel Loan Repayments --}}
        <table class="table table-bordered  datatable2 ">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllKonter"></th>
                    <th>Di Pegang</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Nominal</th>
                    <th>Tanggal Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($konters as $data)
                <tr>
                    <td>
                        <input type="checkbox" class="konterCheckbox" name="selected_ids[]"
                            value="konter-{{ $data->id }}" data-amount="{{ $data->invoice }}">
                    </td>
                    <td>{{ $data->warga->name ?? "Tidak di ketahui" }}</td>
                    <td>{{ $data->code ?? "Tidak di ketahui" }}</td>
                    <td>{{ $data->detail->name ?? "Tidak di ketahui" }}</td>
                    <td>Rp{{ number_format($data->invoice, 0, ',', '.') ?? "Tidak di ketahui" }}</td>
                    <td>{{ $data->updated_at ?? "Tidak di ketahui" }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p><strong>Total Konter Dipilih:</strong> Rp<span id="totalKonter">0</span></p>
    </div>
</div>