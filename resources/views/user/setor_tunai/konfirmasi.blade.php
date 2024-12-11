@extends('user.layout.app')

@section('content')

@if ($deposit->status == "pending")
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Penting !</h5>
    Harap Konfirmasi dengan benar, pastikan data sesuai karena setelah di konfirmasi maka akan masuk ke data dan akan
    masuk ke perhitungan saldo.
</div>
@endif
@if ($deposit->status == "confirmed")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Data pembayaran kas sudah di konfirmasi, dan sudah masuk ke data.
</div>
@endif
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">{{$deposit->submit->name}} ( {{$deposit->code}} )</h3>
        <div class="card-tools">
            @if ($deposit->status == "pending")
            <a class="btn btn-tool" href="{{route('setor-tunai.edit',Crypt::encrypt($deposit->id))}}">
                <i class="fas fa-pencil-alt">
                </i>
            </a>
            <a class="btn btn-tool" href="{{route('setor-tunai.destroy',Crypt::encrypt($deposit->id))}}"
                data-confirm-delete="true">
                <i class="fas fa-trash">
                </i>
            </a>
            @endif
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body p-0">
            <table class="table table-hover text-nowrap table-responsive">
                <tbody>
                    <tr>
                        <td>Kode</td>
                        <td>:</td>
                        <td>{{$deposit->code}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>:</td>
                        <td>{{$deposit->created_at}}</td>
                    </tr>
                    <tr>
                        <td>Nama Penyetor</td>
                        <td>:</td>
                        <td>{{$deposit->submit->name}}</td>
                    </tr>
                    <tr>
                        <td>Jumlah setor</td>
                        <td>:</td>
                        <td>Rp. {{number_format( $deposit->amount, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>
                            @if($deposit->status === 'confirmed')
                            <span class="badge badge-success">Selesai</span>
                            @elseif($deposit->status === 'pending')
                            <span class="badge badge-warning">Menunggu persetujuan <br> Ketua</span>
                            @elseif($deposit->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                            @else
                            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                            @endif
                        </td>
                    </tr>
                    @if ($deposit->status == "confirmed")
                    <tr>
                        <td>Di Konfirmasi Oleh</td>
                        <td>:</td>
                        <td>{{ $deposit->confirm->name}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal di Konfirmasi</td>
                        <td>:</td>
                        <td>{{ $deposit->confirmation_date}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="card-footer">
                <p>
                    Keterangan : <br>
                    {!!$deposit->description!!}
                </p>
            </div>
            <br>
            <table class="table table-bordered table-striped table-responsive datatable1 ">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Nominal</th>
                        <th>Tanggal Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data_kas as $kas)
                    <tr onclick="window.location='{{ route('kas.show',Crypt::encrypt($kas->id)) }}'"
                        style="cursor: pointer;">
                        <td>KAS</td>
                        <td>{{ $kas->code }}</td>
                        <td>{{ $kas->data_warga->name }}</td>
                        <td>Rp{{ number_format($kas->amount, 0, ',', '.') }}</td>
                        <td>{{ $kas->payment_date }}</td>
                    </tr>
                    @endforeach
                    @foreach ($data_loanRepayment as $loan)
                    <tr onclick="window.location='{{ route('bayar-pinjaman.show',Crypt::encrypt($loan->id)) }}'"
                        style="cursor: pointer;">
                        <td>Bayar Pinjaman</td>
                        <td>{{ $loan->code }}</td>
                        <td>{{ $loan->data_warga->name }}</td>
                        <td>Rp{{ number_format($loan->amount, 0, ',', '.') }}</td>
                        <td>{{ $loan->payment_date }}</td>
                    </tr>
                    @endforeach
                    @foreach ($data_konter as $konter)
                    <tr onclick="window.location='{{ route('konter.show',Crypt::encrypt($konter->id)) }}'"
                        style="cursor: pointer;">
                        <td>Konter</td>
                        <td>{{ $konter->code }}</td>
                        <td>{{ $konter->detail->name }}</td>
                        <td>Rp{{ number_format($konter->invoice, 0, ',', '.') }}</td>
                        <td>{{ $konter->created_at }}</td>
                    </tr>
                    @endforeach
                    @foreach ($data_income as $income)
                    <tr onclick="window.location='{{ route('other-income.show',Crypt::encrypt($income->id)) }}'"
                        style="cursor: pointer;">
                        <td>Konter</td>
                        <td>{{ $income->code }}</td>
                        <td>{{ $income->anggaran->name }}</td>
                        <td>Rp{{ number_format($income->amount, 0, ',', '.') }}</td>
                        <td>{{ $income->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
            <!-- Thumbnail Tanda Bukti Transfer -->
            @if ($deposit->payment_method == "transfer")
            <div class="form-group col-6 col-sm-2 justify-between">
                <a href="{{ asset($deposit->receipt_path) }}" data-toggle="lightbox"
                    data-title="Tanda Bukti Transfer - {{$deposit->code}}" data-gallery="gallery">
                    <img src="{{ asset($deposit->receipt_path) }}" class="img-fluid mb-2" alt="white sample" />
                </a>
            </div>
            @endif
        </div>
        @if ($deposit->status != "confirmed")
        @if(Auth::user()->role->name == "Ketua" || Auth::user()->role->name == "Wakil Ketua")
        <form action="{{ route('setor-tunai.confirm',Crypt::encrypt($deposit->id)) }}" method="POST"
            enctype="multipart/form-data" id="adminForm">
            @method('PATCH')
            {{csrf_field()}}

            <input type="hidden" name="amount" value="{{$deposit->amount}}">

            <div class="form-group">
                <label for="status">Satus Konfirmasi</label>
                <select class="select2bs4 @error('status') is-invalid @enderror" style="width: 100%;" name="status"
                    id="status">
                    <option value="">--Pilih status--</option>
                    <option value="confirmed" selected
                        {{ old('status',$deposit->status) == 'confirmed' ? 'selected' : '' }}>
                        Konfirmasi
                    </option>
                    <option value="rejected" {{ old('status',$deposit->status) == 'rejected' ? 'selected' : '' }}>
                        Rejected</option>
                </select>
            </div>
            <!-- Button Submit -->
            <button type="submit" class="btn btn-success" id="submitBtns">Konfirmasi</button>
        </form>
        @endif
        @endif
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <p>
            Catatan : <br>
            - Data di tabel merupakan data yang di depositkan<br>
            - Jika inginmelihat detail data yang di depositkan klik saja<br>
            - Pastikan data sesuai <br>
        </p>
    </div>
</div>
<!-- /.card -->


@endsection