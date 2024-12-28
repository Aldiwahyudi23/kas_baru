<!-- Kode ini untuk isi tabel di dalam index data_admin -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Pinjaman Pribadi</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped datatable1 ">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Kode</th>
                    <th>Nominal</th>
                    <th>Sisa</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 0; ?>
                @foreach($pinjaman as $data)
                <?php $no++; ?>
                <tr>
                    <td>
                        @if($data->status === 'Paid in Full')
                        <a href="{{ route('pinjaman.show',Crypt::encrypt($data->id)) }}">
                            <span class="btn btn-success">Lunas</span>
                        </a>
                        @elseif($data->status === 'pending')
                        <span class="btn btn-warning">Proses</span>
                        @elseif($data->status = ['Acknowledged', 'In Repayment'])
                        <a href="{{ route('bayar-pinjaman.pembayaran',Crypt::encrypt($data->id)) }}">
                            <span class="btn btn-danger">Bayar</span>
                        </a>
                        @elseif($data->status === 'approved_by_chairman')
                        <span class="btn btn-secondary">Disetujui</span>
                        @elseif($data->status === 'disbursed_by_treasurer')
                        <span class="btn btn-secondary">Pencairan</span>
                        @else
                        <span class="btn btn-light">Unknown</span> <!-- default if status is undefined -->
                        @endif
                    </td>
                    <td>{{$data->code}} </td>
                    <td>Rp {{number_format($data->loan_amount,0,',','.')}} </td>
                    <td>Rp {{number_format($data->remaining_balance,0,',','.')}} </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>