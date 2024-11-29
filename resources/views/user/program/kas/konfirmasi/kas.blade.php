@extends('user.layout.app')

@section('content')

@if ($kas_payment->status == "process")
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Penting !</h5>
    Harap Konfirmasi dengan benar, pastikan data sesuai karena setelah di konfirmasi maka akan masuk ke data dan akan
    masuk ke perhitungan saldo.
</div>
@endif
@if ($kas_payment->status == "confirmed")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Data pembayaran kas sudah di konfirmasi, dan sudah masuk ke data.
</div>
@endif
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">{{$kas_payment->data_warga->name}} ( {{$kas_payment->code}} )</h3>
        <div class="card-tools">
            <a class="btn btn-tool" href="{{route('kas.editPengurus',Crypt::encrypt($kas_payment->id))}}">
                <i class="fas fa-pencil-alt">
                </i>
            </a>
            <a class="btn btn-tool" href="{{route('kas.destroyPengurus',Crypt::encrypt($kas_payment->id))}}"
                data-confirm-delete="true">
                <i class="fas fa-trash">
                </i>
            </a>
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
                        <td>{{$kas_payment->code}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>:</td>
                        <td>{{$kas_payment->payment_date}}</td>
                    </tr>
                    <tr>
                        <td>Nama Anggota</td>
                        <td>:</td>
                        <td>{{$kas_payment->data_warga->name}}</td>
                    </tr>
                    <tr>
                        <td>Di Input oleh</td>
                        <td>:</td>
                        <td>{{$kas_payment->submitted->name}}</td>
                    </tr>
                    <tr>
                        <td>Nominal</td>
                        <td>:</td>
                        <td>Rp. {{number_format( $kas_payment->amount, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Pembayaran</td>
                        <td>:</td>
                        <td>{{ $kas_payment->payment_method}}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>
                            @if($kas_payment->status === 'confirmed')
                            <span class="badge badge-success">Selesai</span>
                            @elseif($kas_payment->status === 'process')
                            <span class="badge badge-warning">Menunggu persetujuan <br> Bendahara</span>
                            @elseif($kas_payment->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                            @elseif($kas_payment->status === 'pending')
                            <span class="badge badge-secondary">Pending</span>
                            @else
                            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                            @endif
                        </td>
                    </tr>
                    @if ($kas_payment->status == "confirmed")
                    <tr>
                        <td>Di Konfirmasi Oleh</td>
                        <td>:</td>
                        <td>{{ $kas_payment->confirmed->name}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal di Konfirmasi</td>
                        <td>:</td>
                        <td>{{ $kas_payment->confirmation_date}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="card-footer">
                <p>
                    Keterangan : <br>
                    {{$kas_payment->description}}
                </p>
            </div>
            <br>
            <!-- Thumbnail Tanda Bukti Transfer -->
            @if ($kas_payment->payment_method == "transfer")
            <div class="form-group col-6 col-sm-2 justify-between">
                <a href="{{ asset($kas_payment->transfer_receipt_path) }}" data-toggle="lightbox"
                    data-title="Tanda Bukti Transfer - {{$kas_payment->code}}" data-gallery="gallery">
                    <img src="{{ asset($kas_payment->transfer_receipt_path) }}" class="img-fluid mb-2"
                        alt="white sample" />
                </a>
            </div>
            @endif
        </div>
        @if ($kas_payment->status != "confirmed")
        @if(Auth::user()->role->name == "Bendahara" || Auth::user()->role->name == "Wakil Bendahara" || Auth::user()->role->name == "Sekretaris" || Auth::user()->role->name == "Wakil Sekretaris" || Auth::user()->role->name == "Ketua" || Auth::user()->role->name == "Wakil Ketua")
        <form action="{{ route('kas.confirm',Crypt::encrypt($kas_payment->id)) }}" method="POST"
            enctype="multipart/form-data" id="adminForm">
            @method('PATCH')
            {{csrf_field()}}

            <input type="hidden" name="data_warga_id" value="{{$kas_payment->data_warga_id}}">
            <input type="hidden" name="amount" value="{{$kas_payment->amount}}">
            <input type="hidden" name="payment_method" value="{{$kas_payment->payment_method}}">
            <input type="hidden" name="description" value="{{$kas_payment->description}}">
            <input type="hidden" name="submitted_by" value="{{$kas_payment->submitted_by}}">
            <input type="hidden" name="is_deposited" value="{{$kas_payment->is_deposited}}">
            <input type="hidden" name="code" value="{{$kas_payment->code}}">

            <div class="form-group">
                <label for="status">Satus Konfirmasi</label>
                <select class="select2bs4 @error('status') is-invalid @enderror" style="width: 100%;" name="status"
                    id="status">
                    <option value="">--Pilih status--</option>
                    <option value="pending" {{ old('status',$kas_payment->status) == 'pending' ? 'selected' : '' }}>
                        Pending</option>
                    <option value="confirmed" selected
                        {{ old('status',$kas_payment->status) == 'confirmed' ? 'selected' : '' }}>
                        Konfirmasi
                    </option>
                    <option value="rejected" {{ old('status',$kas_payment->status) == 'rejected' ? 'selected' : '' }}
                        disabled>
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
            - Segera Konfirmasi Pengajuan data di atas <br>
            - Pastikan data sesuai <br>
        </p>
        <p><i> Pilih Pending jika data ada yang tidak sesuai dan sementara sedang di tinjau <br>
                Pilih Reject jika data sudah benar tidak sesuai</i></p>
    </div>
</div>
<!-- /.card -->


@endsection