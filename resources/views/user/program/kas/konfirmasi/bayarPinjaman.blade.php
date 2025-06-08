@extends('user.layout.app')

@section('content')

@if ($pembayarPinjaman->status == "process")
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Penting !</h5>
    Harap Konfirmasi dengan benar, pastikan data sesuai karena setelah di konfirmasi maka akan masuk ke data dan akan
    masuk ke perhitungan saldo.
</div>
@endif
@if ($pembayarPinjaman->status == "confirmed")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Data pembayaran kas sudah di konfirmasi, dan sudah masuk ke data.
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
        <div class="alert 
        @if($hitungWaktu <= 3 ) alert-danger 
        @elseif($hitungWaktu <= 14) alert-warning 
        @else alert-success
        @endif alert-dismissible">
            <center>
                @if($hitungWaktu == 0) Jatuh Tempo
                @elseif($hitungWaktu <= -1) Lewat {{ $hitungWaktu }} hari segera bayar @else {{ $hitungWaktu }} hari
                    Lagi @endif </center>
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
                            <td>Tanggal Jatuh Tempo</td>
                            <td>:</td>
                            <td>{{$pinjaman->deadline_date}}</td>
                        </tr>
                        <tr>
                            <td>Di Input oleh</td>
                            <td>:</td>
                            <td>{{$pinjaman->sekretaris->name}}</td>
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
                        @if ($pinjaman->overpayment_balance > 0 || $waktuPembayaran > $waktuDitentukan)
                        <tr>
                            <td>Lebih</td>
                            <td>:</td>
                            @if ($pinjaman->overpayment_balance == 0) <td>Pembayaran lebih dari {{$waktuDitentukan}}
                                hari,
                                sesuai kesepakatan
                                !!!</td>
                            @else
                            <td>Rp. {{number_format( $pinjaman->overpayment_balance, 0, ',', '.')}}</td>
                            @endif
                        </tr>
                        @endif
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
                                @elseif($pinjaman->status === 'disbursed_by_treasurer')
                                <span class="badge badge-secondary">Sudah di cairkan <br> Segera konfirmasi penerimaan
                                </span>
                                @elseif($pinjaman->status === 'In Repayment')
                                <span class="badge badge-success">Dalah Proses Cicil</span>
                                @elseif($pinjaman->status === 'Paid in Full')
                                <span class="badge badge-success">Selesai/Lunas</span>
                                @else
                                <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
               
            </div>
           
            <hr>

            <!-- Tampilkan data pembayaran -->
            @if (in_array($pinjaman->status, ['In Repayment','Acknowledged']))
            <p class=" text-center">Pembayaran Pinjaman</p>
            <!-- Jika tidak ada pembayaran tampilkan ini  -->
            @if (!$bayarPinjaman || $bayarPinjaman->isEmpty())
            <p class="text-muted text-center">Belum ada pembayaran yang masuk</p>
            @endif
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Jatuh tempo pembayaran <b> {{$pinjaman->deadline_date}}</b> <br>
                Pembayaran {{$bayarPinjaman->count()}} kali,
                Uang yang di terima <b>Rp {{number_format($bayarPinjaman->sum('amount'),0,',','.')}}</b> <br>
            </div>
            @endif
            <ul class="list-group list-group-unbordered mb-3">
                @foreach ($bayarPinjaman as $data)
                <li class="list-group-item"
                    onclick="window.location.href='{{ route('bayar-pinjaman.show',Crypt::encrypt($data->id)) }}'">
                    <b>{{ $data->created_at }}
                        <br>


                        @if($data->status === 'confirmed')
                        <span class="badge badge-success">Dikonfirmasi</span>
                        @elseif($data->status === 'process')
                        <span class="badge badge-warning">Menunggu Dikonfirmasi <br> Bendahara</span>
                        @elseif($data->status === 'rejected')
                        <span class="badge badge-danger">Rejected</span>
                        @elseif($data->status === 'pending')
                        <span class="badge badge-secondary">Pending</span>
                        @else
                        <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                        @endif
                    </b>
                    <p class="float-right">
                        Rp.{{ number_format($data->amount, 0, ',', '.') }}
                    </p>
                </li>
                @endforeach
            </ul>
        </div>
    
 <div class="card-header">
        <h3 class="card-title">{{$pembayarPinjaman->data_warga->name}} ( {{$pembayarPinjaman->code}} )</h3>
        <div class="card-tools">
            @if ($pembayarPinjaman->status == "process")
            <a class="btn btn-tool" href="{{route('bayar-pinjaman.editPengurus',Crypt::encrypt($pembayarPinjaman->id))}}">
                <i class="fas fa-pencil-alt">
                </i>
            </a>
            <a class="btn btn-tool"
                href="{{route('bayar-pinjaman.destroyPengurus',Crypt::encrypt($pembayarPinjaman->id))}}"
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
        <div class="card-body  p-0">
            <table class="table table-hover text-nowrap table-responsive">
                <tbody>
                    <tr>
                        <td>Kode</td>
                        <td>:</td>
                        <td>{{$pembayarPinjaman->code}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>:</td>
                        <td>{{$pembayarPinjaman->payment_date}}</td>
                    </tr>
                    <tr>
                        <td>Nama Anggota</td>
                        <td>:</td>
                        <td>{{$pembayarPinjaman->data_warga->name}}</td>
                    </tr>
                    <tr>
                        <td>Di Input oleh</td>
                        <td>:</td>
                        <td>{{$pembayarPinjaman->submitted->name}}</td>
                    </tr>
                    <tr>
                        <td>Nominal</td>
                        <td>:</td>
                        <td>Rp. {{number_format( $pembayarPinjaman->amount, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Pembayaran</td>
                        <td>:</td>
                        <td>{{ $pembayarPinjaman->payment_method}}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>
                            @if($pembayarPinjaman->status === 'confirmed')
                            <span class="badge badge-success">Dikonfirmasi</span>
                            @elseif($pembayarPinjaman->status === 'process')
                            <span class="badge badge-warning">Menunggu Dikonfirmasi <br> Bendahara</span>
                            @elseif($pembayarPinjaman->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                            @elseif($pembayarPinjaman->status === 'pending')
                            <span class="badge badge-secondary">Pending</span>
                            @else
                            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                            @endif
                        </td>
                    </tr>
                    @if ($pembayarPinjaman->status == "confirmed")
                    <tr>
                        <td>Di Konfirmasi Oleh</td>
                        <td>:</td>
                        <td>{{ $pembayarPinjaman->confirmed->name}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal di Konfirmasi</td>
                        <td>:</td>
                        <td>{{ $pembayarPinjaman->confirmation_date}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="card-footer">
                <p>
                    Keterangan : <br>
                    {{$pembayarPinjaman->description}}
                </p>
            </div>
            <br>
            <!-- Thumbnail Tanda Bukti Transfer -->
            @if ($pembayarPinjaman->payment_method == "transfer")
            <div class="form-group col-6 col-sm-2 justify-between">
                <a href="{{ asset($pembayarPinjaman->transfer_receipt_path) }}" data-toggle="lightbox"
                    data-title="Tanda Bukti Transfer - {{$pembayarPinjaman->code}}" data-gallery="gallery">
                    <img src="{{ asset($pembayarPinjaman->transfer_receipt_path) }}" class="img-fluid mb-2"
                        alt="white sample" />
                </a>
            </div>
            @endif
        </div>
        @if ($pembayarPinjaman->status != "confirmed")
        <form action="{{ route('bayar-pinjaman.confirm',Crypt::encrypt($pembayarPinjaman->id)) }}" method="POST"
            enctype="multipart/form-data" id="adminForm">
            @method('PATCH')
            {{csrf_field()}}

            <input type="hidden" name="data_warga_id" value="{{$pembayarPinjaman->data_warga_id}}">
            <input type="hidden" name="amount" value="{{$pembayarPinjaman->amount}}">
            <input type="hidden" name="payment_method" value="{{$pembayarPinjaman->payment_method}}">
            <input type="hidden" name="description" value="{{$pembayarPinjaman->description}}">
            <input type="hidden" name="submitted_by" value="{{$pembayarPinjaman->submitted_by}}">
            <input type="hidden" name="is_deposited" value="{{$pembayarPinjaman->is_deposited}}">
            <input type="hidden" name="code" value="{{$pembayarPinjaman->code}}">

            <div class="form-group">
                <label for="status">Satus Konfirmasi</label>
                <select class="select2bs4 @error('status') is-invalid @enderror" style="width: 100%;" name="status"
                    id="status">
                    <option value="">--Pilih status--</option>
                    <option value="pending" {{ old('status',$pembayarPinjaman->status) == 'pending' ? 'selected' : '' }}>
                        Pending</option>
                    <option value="confirmed" selected
                        {{ old('status',$pembayarPinjaman->status) == 'confirmed' ? 'selected' : '' }}>
                        Konfirmasi
                    </option>
                    <option value="rejected" {{ old('status',$pembayarPinjaman->status) == 'rejected' ? 'selected' : '' }}
                        disabled>
                        Rejected</option>
                </select>
            </div>
            <!-- Button Submit -->
            <button type="submit" class="btn btn-success" id="submitBtns">Konfirmasi</button>
        </form>
        @endif
    </div>

    <!-- /.card-body -->
    <div class="card-footer">
        <p>
            Catatan : <br>
            - Segera Konfirmasi Pengajuan data di atas <br>
            - Pastikan data sesuai <br>
        </p>
        <p><i> Pilih Pending jika data ada yang tidak sesuai dan sementara sedang di tinjau <br>
                Pilih Reject jika data sudah benar tidak sesuai</i></p>
    </div>
</div>
<!-- /.card -->


@endsection