@extends('user.layout.app')

@section('content')

@if ($pinjaman->status != "Acknowledged")
<div class="alert alert-secondary alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Penting !</h5>
    halaman ini untuk membayar pinjaman yang masih aktif
</div>
@endif
@if ($pinjaman->status == "Acknowledged")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Pinjaman sudah di terima oleh pengaju.
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
@endif
@endif
@endif
@if ($hitungWaktu <= 14) <div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Pemberitahuan !!!</h5>
    Waktu Pinjaman {{$hitungWaktu}} hari lagi, Segera bayar Lunasi agar uang bisa bergulir <br>
    - Jika belum bisa bayar, setelah sisa 7 hari bisa mengajukan kembali namun harus ada masuk dulu ke pinjaman
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
                <div class="card-footer">
                    <p>
                        Keterangan : <br>
                        {!!$pinjaman->description!!}
                    </p>
                </div>
            </div>
            @if (isset($pinjaman->disbursement_receipt_path))
            <div class="form-group col-6 col-sm-2 justify-between">
                <a href="{{ asset($pinjaman->disbursement_receipt_path) }}" data-toggle="lightbox"
                    data-title="Tanda Bukti Transfer - {{$pinjaman->code}}" data-gallery="gallery">
                    <img src="{{ asset($pinjaman->disbursement_receipt_path) }}" class="img-fluid mb-2"
                        alt="white sample" />
                </a>
            </div>
            @endif
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
                <p class="text-muted">Segera konfirmasi untuk melakukan pembayaran pinjaman</p>
            </form>
            @endif

            <!-- DI bagian bawah halaman untuk form bayar pinjam -->
            @if (in_array($pinjaman->status, ['Acknowledged', 'In Repayment']))
            <!-- untuk form pembayaran -->
            @if(in_array(Auth::user()->role->name, ['Ketua','Wakil Ketua','Bendahara','Wakil
            Bendahara','Sekretaris','Wakil
            Sekretaris']) || Auth::user()->data_warga_id == $pinjaman->data_warga_id)

            @if (isset($cek_pembayaran))
            <hr>
            {!!$layout_form->b_pinjam_proses!!}
            @elseif ($cek_pengajuan)
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-info"></i>Pengajuan ke 2 </h5>
                Pengajuan Pinjaman ke 2, dengan waktu satu bulan setengan sedang di proses, menunggu konfirmasi
                <!-- Untuk batal -->
                <form action="{{ route('pinjaman-ke-dua.reject',Crypt::encrypt($cek_pengajuan->id)) }}" method="POST"
                    enctype="multipart/form-data" id="adminForm">
                    @method('PATCH')
                    {{csrf_field()}}
                    <input type="hidden" name="status" value="rejected">
                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-warning" id="submitBtns">Batal</button>
                </form>
            </div>
            @else
            @include('user.program.kas.form.bayarPinjaman')
            @endif

            @endif
            @endif
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <p>
                Catatan : <br>
                - Selalu bayar tepat waktu, dan di usahakan setiap bulannya bayar (cicil)<br>
                - Setiap satu bulan sekali ada pesan pemeritahuan <br>
            </p>
            <p><i>Anggaran yang sudah di ajukan semoga bermanfaat</i></p>
        </div>
    </div>
    <!-- /.card -->


    @endsection