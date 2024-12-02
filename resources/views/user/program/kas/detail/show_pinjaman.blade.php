@extends('user.layout.app')

@section('content')

@if (in_array($pinjaman->status , ['pending', 'approved_by_chairman', 'disbursed_by_treasurer']))
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Proses !</h5>
    Pengajuan Pinjaman masih dalam Proses, harap bersabar dan slalu cek status pengajuan di bawah.
</div>
@endif
@if (in_array($pinjaman->status , ['Acknowledged','In Repayment']))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i>Sudah di terima </h5>
    Pinjaman sudah di setujui dan sudah di terima.
</div>
@endif

@if ($waktuPembayaran > $waktuDitentukan)
@if ($pinjaman->remaining_balance == 0)
@if ($pinjaman->overpayment_balance == 0)
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Pemberitahuan !</h5>
    Pembayaran lebih dari {{$waktuDitentukan}} hari, Pelunasan di hari ke {{$waktuPembayaran}} sesuai kesepakatan di
    minta
    kerjasamanya status masih belum lunas.
</div>
@else
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i>Selesai </h5>
    Pembayaran pinjaman {{$waktuPembayaran}} Hari <br> Terima Kasih sudah bekerjasama, Semoga berkah selalu dan lancar
    segala rejekinya.
</div>
@endif
@endif
@else
@if ($hitungWaktu <= 14) <div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Pemberitahuan !!!</h5>
    Waktu Pinjaman {{$hitungWaktu}} hari lagi, Segera bayar Lunasi agar uang bisa bergulir <br>
    - Jika belum bisa bayar, setelah sisa 7 hari bisa mengajukan kembali namun harus ada masuk dulu ke pinjaman
    </div>
    @endif
    @if($pinjaman->status == "Paid in Full")
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5><i class="icon fas fa-check"></i>Selesai </h5>
        Yeeee... <br> Pembayaran pinjaman {{$waktuPembayaran}} Hari <br> Terima Kasih sudah bekerjasama, Semoga berkah
        selalu
        dan lancar
        segala rejekinya.
    </div>
    @endif
    @endif

    <!-- SELECT2 EXAMPLE -->
    <div class="card card-default">
        <div class="card-header">
            <h3 class="card-title">{{$pinjaman->sekretaris->name}} ( {{$pinjaman->code}} )</h3>
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
                            <td>Tanggal Jatuh Tempo</td>
                            <td>:</td>
                            <td>{{$pinjaman->deadline_date}}</td>
                        </tr>
                        <tr>
                            <td>Nama Anggaran</td>
                            <td>:</td>
                            <td>{{$pinjaman->anggaran->name}}</td>
                        </tr>
                        <tr>
                            <td>Nama Warga</td>
                            <td>:</td>
                            <td>{{$pinjaman->warga->name}}</td>
                        </tr>
                        <tr>
                            <td>Di Input oleh</td>
                            <td>:</td>
                            <td>{{$pinjaman->sekretaris->name}}</td>
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
                            @if ($pinjaman->overpayment_balance == 0)
                            <td>Pembayaran lebih dari {{$waktuDitentukan}} hari,
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
                                @elseif($pinjaman->status === 'In Repayment')
                                <span class="badge badge-success">Proses Cicil</span>
                                @elseif($pinjaman->status === 'Paid in Full')
                                <span class="badge badge-success">Selesai / Lunas</span>
                                @elseif($pinjaman->status === 'disbursed_by_treasurer')
                                <span class="badge badge-secondary">Sudah di cairkan, <br> Menunggu konfirmasi bahwa
                                    uang
                                    telah
                                    di terima </span>
                                @else
                                <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td>Di Konfirmasi Oleh</td>
                            <td>:</td>
                            <td>{{ isset($pinjaman->ketua->name) ? $pinjaman->ketua->name : 'Proses...' }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal di Konfirmasi</td>
                            <td>:</td>
                            <td>{{ isset($pinjaman->approved_date) ? $pinjaman->approved_date : 'Proses...'}}</td>
                        </tr>
                        <tr>
                            <td>Pencarian</td>
                            <td>:</td>
                            <td>{{ isset($pinjaman->bendahara->name) ? $pinjaman->bendahara->name : 'Proses...'}}</td>
                        </tr>
                        <tr>
                            <td>Tanggal di cairkan</td>
                            <td>:</td>
                            <td>{{ isset($pinjaman->disbursed_date) ? $pinjaman->disbursed_date : 'Proses...'}}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="card-footer">
                    <p>
                        Keterangan : <br>
                        {!!$pinjaman->description!!}
                    </p>
                </div>
                <br>
                <!-- Tampilkan Tanda bukti pencairan -->
                @if (isset($pinjaman->disbursement_receipt_path))
                <div class="form-group col-6 col-sm-2 justify-between">
                    <a href="{{ asset($pinjaman->disbursement_receipt_path) }}" data-toggle="lightbox"
                        data-title="Tanda Bukti Transfer - {{$pinjaman->code}}" data-gallery="gallery">
                        <img src="{{ asset($pinjaman->disbursement_receipt_path) }}" class="img-fluid mb-2"
                            alt="white sample" />
                    </a>
                </div>
                @endif
                <!-- Tampilkan data setiap pembayaran pinjaman -->
                <p class=" text-center">Pembayaran Pinjaman</p>
                @if (!$bayarPinjaman || $bayarPinjaman->isEmpty())
                <p class="text-muted text-center">Belum ada pembayaran yang masuk</p>
                @endif
                <!-- Jika pembayaran sudah selesai tampilkan ini -->
                @if ($pinjaman->status == "Paid in Full")
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    Pembayaran telah <b>Lunas</b>, di bayar dalam {{$bayarPinjaman->count()}} kali pembayaran. <br>
                    Uang yang di terima <b>Rp {{number_format($bayarPinjaman->sum('amount'),0,',','.')}}</b> <br>
                    Nominal Pinjaman <b>Rp {{number_format( $pinjaman->loan_amount, 0, ',', '.')}}</b> <br>
                    @if ($pinjaman->overpayment_balance > 0 )
                    Uang <b>kasih sayang</b> yang di berikan <b>Rp
                        {{number_format( $pinjaman->overpayment_balance, 0, ',', '.')}}</b> ,
                    Uangnya sudah kita terima <i>semoga bisa di balas oleh yang maha Kuasa</i>.
                    @endif
                </div>
                <!-- Pembayaran ini sedang di perpanjang -->
                @if ($pinjamanKeDua)
                <div class="alert alert-secondary alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    Data Pinjaman ini di lanjut pinjaman ke dua, sesuai data di atas yang menunjukan sisa pinjaman masih
                    Rp {{number_format($pinjaman->remaining_balance,0,',','.')}} di ambil dari sisa pembayaran
                    sebelumnya. <br>
                    <b>Alasan :</b>
                    <br>
                    {{$pinjamanKeDua->reason}}
                    <hr>
                    <a href="{{ route('pinjaman.show',Crypt::encrypt($pinjamanKeDua->new_loan_id)) }}">
                        <span class="btn btn-info">Lihat Pinjaman ke dua</span>
                    </a>

                </div>
                @endif

                @endif
                <ul class="list-group list-group-unbordered mb-3">
                    @foreach ($bayarPinjaman as $data)
                    <li class="list-group-item"
                        onclick="window.location.href='{{ route('bayar-pinjaman.show',Crypt::encrypt($data->id)) }}'">
                        <b>{{ $data->created_at }}</b>
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
                        <p class="float-right">Rp.
                            {{ number_format($data->amount, 0, ',', '.') }}
                        </p>
                    </li>

                    @endforeach
                </ul>
                <!-- Munculkan tombol konfirmasi uang sudah di terima ketika uang sudah di cairkan  -->
                @if ($pinjaman->status == "disbursed_by_treasurer")
                <form action="{{ route('pinjaman.acknowledged',Crypt::encrypt($pinjaman->id)) }}" method="POST"
                    enctype="multipart/form-data" id="adminForm">
                    @method('PATCH')
                    {{csrf_field()}}
                    <input type="hidden" name="status" value="Acknowledged">
                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Konfirmasi
                        Uang sudah di
                        terima</button>
                </form>
                @endif
            </div>
        </div>

        <!-- /.card-body -->
        <div class="card-footer">
            <p>
                Catatan : <br>
                - Selalu bayar tepat waktu, dan di usahakan setiap bulannya bayar (cicil)<br>
                - Setiap Pengajuan telah di atur<br>
            </p>
            <p><i> Semoga Amanah, dan Kerjasamanya</i></p>
        </div>
    </div>
    <!-- /.card -->


    @endsection