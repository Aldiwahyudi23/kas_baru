@extends('user.layout.app')

@section('content')

<div class="alert alert-info alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5> Catatan !</h5>
    Pinjaman masih belum lunas dan sudah berjalan {{$hitungWaktu}} hari, data di bawah adalah data pinjaman yang sebelum
    nya. Periksa data denga baik.
</div>
@if ($pinjamanKeDua->status == "rejected")
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-ban"></i> Batal</h5>
    Pengajuan sudah di batalkan.
</div>
@endif

<div class="row">
    <div class="col-12 col-sm-6">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">{{$pinjaman->anggaran->name}} <br> ( {{$pinjaman->code}} )</h3>
                <div class="card-tools">
                    <a class="btn btn-tool" href="{{route('pinjaman.show',Crypt::encrypt($pinjaman->id))}}">
                        <i class="fas fa-pencil-alt"></i> Lihat data
                    </a>
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
                                <td>Sisa Pinjaman</td>
                                <td>:</td>
                                <td>Rp. {{number_format( $pinjaman->remaining_balance, 0, ',', '.')}}</td>
                            </tr>
                            <tr>
                                <td>Lebih</td>
                                <td>:</td>
                                <td>Rp. {{number_format( $pinjaman->overpayment_balance, 0, ',', '.')}}</td>
                            </tr>
                            <tr>
                                <td>Status Terakhir</td>
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
                                    <span class="badge badge-light">Unknown</span>
                                    <!-- default if status is undefined -->
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>


                </div>
                <!-- untuk ketua -->
                @if(Auth::user()->role->name == "Ketua" || Auth::user()->role->name == "Wakil Ketua")
                <!-- Ketua bisa mengkonfimrasi ketika status hanya peding -->
                @if ($pinjaman->status == "pending")
                <form action="{{ route('pinjaman.approved',Crypt::encrypt($pinjaman->id)) }}" method="POST"
                    enctype="multipart/form-data" id="adminForm">
                    @method('PATCH')
                    {{csrf_field()}}
                    <input type="hidden" name="status" value="approved_by_chairman">
                    <input type="hidden" name="data_warga_id" value="{{Auth::user()->data_warga_id}}">

                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Setujui</button>
                </form>
                @endif
                @endif

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <p>
                    Pinjaman sudah berjalan {{$hitungWaktu}} hari dari pengajuan, baru di bayar Rp
                    {{number_format($jumlahBayarPinjaman,0,',','.')}} ada sisa Rp
                    {{number_format($pinjaman->remaining_balance,0,',','.')}}.
                </p>
                <p>
                    Ketentuan yang sudah di sepakati untuk perpanjangan atau pinjaman ke dua ini, bisa di lakukan dengan
                    syarat harus ada tanda pelunasan dan uang kasih sayang sesuai kesepakatan.
                </p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">{{$pinjaman->anggaran->name}} <br> ( {{$pinjaman->code}} )</h3>
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
                                <td>Status Pengajuan</td>
                                <td>:</td>
                                <td>
                                    @if($pinjamanKeDua->status === 'approved')
                                    <span class="badge badge-success">Sudah di konfirmasi Ketua, nunggu konfirmasi dari
                                        warga</span>
                                    @elseif($pinjamanKeDua->status === 'pending')
                                    <span class="badge badge-warning">Menunggu persetujuan Ketua</span>
                                    @elseif($pinjamanKeDua->status === 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                    @else
                                    <span class="badge badge-light">Unknown</span>
                                    <!-- default if status is undefined -->
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Tanggal Pengajuan</td>
                                <td>:</td>
                                <td>{{$pinjamanKeDua->extension_date}}</td>
                            </tr>
                            <tr>
                                <td>Data Warga</td>
                                <td>:</td>
                                <td>{{$pinjamanKeDua->data_warga->name}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <p><b>Alasan Pengajuan</b><br>
                    {{$pinjamanKeDua->reason}}
                </p>
                <p>
                    {!!$pinjamanKeDua->notes!!}
                </p>
                <div class="alert alert-secondary">
                    <h5><i class="icon fas fa-info"></i> Informasi</h5>
                    Jika di setujui maka data pinjaman sebelumnya akan berubah <br>
                    - status menjadi <b>Lunas</b> <br>
                    - Sisa berubah jadi 0, dan <br>
                    - Uang lebih akan di tambahkan sesuai kesepakatan yang di ambil dari pembayaran sebelumnya.
                </div>
                <div class="alert alert-secondary">
                    Untuk data Pinjaman baru <br>
                    - status akan menjadi <i>Menunggu Konfirmasi penerima</i> <br>
                    - Nominal akan di ambil dari sisa setelah di ambil dari uang kasih sayang <br>
                    - Waktu Jatuh tempo akan di kurangi.
                    - Data yang lain sama
                </div>
                <!-- untuk ketua -->
                @if(Auth::user()->role->name == "Ketua" || Auth::user()->role->name == "Wakil Ketua")
                <!-- Ketua bisa mengkonfimrasi ketika status hanya peding -->
                @if ($pinjamanKeDua->status == "pending")
                <form action="{{ route('pinjaman-ke-dua.confirm',Crypt::encrypt($pinjamanKeDua->id)) }}" method="POST"
                    enctype="multipart/form-data" id="adminForm">
                    @method('PATCH')
                    {{csrf_field()}}
                    <input type="hidden" name="pinjaman_id" value="{{$pinjaman->id}}">
                    <input type="hidden" name="pembayaran" value="{{$jumlahBayarPinjaman}}">

                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Setujui dan Buat Pinjaman
                        Baru</button>
                </form>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>

@endsection