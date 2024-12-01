@extends('user.layout.app')

@section('content')

@if ($pinjaman->status != "Acknowledged")
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Penting !</h5>
    Harap Konfirmasi dengan benar, data pinjaman yang sudah diAjukan harus berisi keterangan yang detail untuk sebuah
    laporan.
</div>
@endif
@if ($pinjaman->status == "Acknowledged")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Data Pengeluaran sudah di keluarkan sesuai data di bawah.
</div>
@endif
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">{{$pinjaman->anggaran->name}} ( {{$pinjaman->code}} )</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body  p-0">
            <table class="table table-hover text-nowrap table-responsive">
                <tbody>
                    <tr>
                        <td>Kode</td>
                        <td>:</td>
                        <td>{{$pinjaman->code}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>:</td>
                        <td>{{$pinjaman->created_at}}</td>
                    </tr>
                    <tr>
                        <td>Data Warga</td>
                        <td>:</td>
                        <td>{{$pinjaman->warga->name}}</td>
                    </tr>
                    <tr>
                        <td>Nominal</td>
                        <td>:</td>
                        <td>Rp. {{number_format( $pinjaman->loan_amount, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Sisa</td>
                        <td>:</td>
                        <td>Rp. {{number_format( $pinjaman->remaining_balance, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>
                            @if($pinjaman->status === 'Acknowledged')
                            <span class="badge badge-success">Uang sudah diterima</span>
                            @elseif($pinjaman->status === 'pending')
                            <span class="badge badge-warning">Menunggu persetujuan Ketua</span>
                            @elseif($pinjaman->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                            @elseif($pinjaman->status === 'approved_by_chairman')
                            <span class="badge badge-secondary">Proses Pencairan oleh Bendahara</span>
                            @elseif($pinjaman->status === 'In Repayment')
                            <span class="badge badge-success">Proses Cicil</span>
                            @elseif($pinjaman->status === 'Paid in Full')
                            <span class="badge badge-success">Selesai / Lunas</span>
                            @elseif($pinjaman->status === 'disbursed_by_treasurer')
                            <span class="badge badge-secondary">Sudah di cairkan, <br> Menunggu konfirmasi bahwa uang
                                telah
                                di terima </span>
                            @else
                            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="card-footer">
                <p>
                    Keterangan : <br>
                    {!!$pinjaman->description!!}
                </p>
            </div>
        </div>


        <form action="{{ route('pinjaman-ke-dua.store') }}" method="POST" enctype="multipart/form-data" id="adminForm">
            @csrf

            <input type="hidden" name="loan_id" value="{{$pinjaman->id}}">
            <input type="hidden" name="data_warga_id" value="{{$pinjaman->data_warga_id}}">
            <input type="hidden" name="loan_amount" value="{{$pinjaman->loan_amount}}">
            <input type="hidden" name="remaining_balance" value="{{$pinjaman->remaining_balance}}">
            <input type="hidden" name="overpayment_balance" value="{{$pinjaman->overpayment_balance}}">
            <input type="hidden" name="status" value="{{$pinjaman->status}}">


            <div class="form-group">
                <label for="reason" class="col-sm-12 col-form-label">Berikan Alasan </label>
                <div class="col-sm-12">
                    <textarea class="form-control col-12 @error('reason') is-invalid @enderror" name="reason"
                        id="reason">{{ old('reason') }}</textarea>
                </div>
            </div>

            <!-- Button Submit -->
            <button type="submit" class="btn btn-warning" id="submitBtns">Ajukan Pinjaman ke 2</button>
        </form>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <p>
            Catatan : <br>
            - Data yang di input di sesuaikan dengan anggaran yang sudah di sepakati <br>
            - Pastikan data sesuai <br>
        </p>
        <p><i>Anggaran yang sudah di ajukan semoga bermanfaat</i></p>
    </div>
</div>
<!-- /.card -->


@endsection