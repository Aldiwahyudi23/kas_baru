@extends('user.layout.app')

@section('content')

@if ($bayarPinjaman->status == "process")
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Penting !</h5>
    Harap Konfirmasi dengan benar, pastikan data sesuai karena setelah di konfirmasi maka akan masuk ke data dan akan
    masuk ke perhitungan saldo.
</div>
@endif
@if ($bayarPinjaman->status == "confirmed")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Data pembayaran kas sudah di konfirmasi, dan sudah masuk ke data.
</div>
@endif
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">{{$bayarPinjaman->data_warga->name}} ( {{$bayarPinjaman->code}} )</h3>
        <div class="card-tools">
            @if ($bayarPinjaman->status == "process")
            <a class="btn btn-tool" href="{{route('bayar-pinjaman.editPengurus',Crypt::encrypt($bayarPinjaman->id))}}">
                <i class="fas fa-pencil-alt">
                </i>
            </a>
            <a class="btn btn-tool"
                href="{{route('bayar-pinjaman.destroyPengurus',Crypt::encrypt($bayarPinjaman->id))}}"
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
                        <td>{{$bayarPinjaman->code}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>:</td>
                        <td>{{$bayarPinjaman->payment_date}}</td>
                    </tr>
                    <tr>
                        <td>Nama Anggota</td>
                        <td>:</td>
                        <td>{{$bayarPinjaman->data_warga->name}}</td>
                    </tr>
                    <tr>
                        <td>Di Input oleh</td>
                        <td>:</td>
                        <td>{{$bayarPinjaman->submitted->name}}</td>
                    </tr>
                    <tr>
                        <td>Nominal</td>
                        <td>:</td>
                        <td>Rp. {{number_format( $bayarPinjaman->amount, 0, ',', '.')}}</td>
                    </tr>
                    <tr>
                        <td>Pembayaran</td>
                        <td>:</td>
                        <td>{{ $bayarPinjaman->payment_method}}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>
                            @if($bayarPinjaman->status === 'confirmed')
                            <span class="badge badge-success">Dikonfirmasi</span>
                            @elseif($bayarPinjaman->status === 'process')
                            <span class="badge badge-warning">Menunggu Dikonfirmasi <br> Bendahara</span>
                            @elseif($bayarPinjaman->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                            @elseif($bayarPinjaman->status === 'pending')
                            <span class="badge badge-secondary">Pending</span>
                            @else
                            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                            @endif
                        </td>
                    </tr>
                    @if ($bayarPinjaman->status == "confirmed")
                    <tr>
                        <td>Di Konfirmasi Oleh</td>
                        <td>:</td>
                        <td>{{ $bayarPinjaman->confirmed->name}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal di Konfirmasi</td>
                        <td>:</td>
                        <td>{{ $bayarPinjaman->confirmation_date}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="card-footer">
                <p>
                    Keterangan : <br>
                    {{$bayarPinjaman->description}}
                </p>
            </div>
            <br>
            <!-- Thumbnail Tanda Bukti Transfer -->
            @if ($bayarPinjaman->payment_method == "transfer")
            <div class="form-group col-6 col-sm-2 justify-between">
                <a href="{{ asset($bayarPinjaman->transfer_receipt_path) }}" data-toggle="lightbox"
                    data-title="Tanda Bukti Transfer - {{$bayarPinjaman->code}}" data-gallery="gallery">
                    <img src="{{ asset($bayarPinjaman->transfer_receipt_path) }}" class="img-fluid mb-2"
                        alt="white sample" />
                </a>
            </div>
            @endif
        </div>
        @if ($bayarPinjaman->status != "confirmed")
        <form action="{{ route('bayar-pinjaman.confirm',Crypt::encrypt($bayarPinjaman->id)) }}" method="POST"
            enctype="multipart/form-data" id="adminForm">
            @method('PATCH')
            {{csrf_field()}}

            <input type="hidden" name="data_warga_id" value="{{$bayarPinjaman->data_warga_id}}">
            <input type="hidden" name="amount" value="{{$bayarPinjaman->amount}}">
            <input type="hidden" name="payment_method" value="{{$bayarPinjaman->payment_method}}">
            <input type="hidden" name="description" value="{{$bayarPinjaman->description}}">
            <input type="hidden" name="submitted_by" value="{{$bayarPinjaman->submitted_by}}">
            <input type="hidden" name="is_deposited" value="{{$bayarPinjaman->is_deposited}}">
            <input type="hidden" name="code" value="{{$bayarPinjaman->code}}">

            <div class="form-group">
                <label for="status">Satus Konfirmasi</label>
                <select class="select2bs4 @error('status') is-invalid @enderror" style="width: 100%;" name="status"
                    id="status">
                    <option value="">--Pilih status--</option>
                    <option value="pending" {{ old('status',$bayarPinjaman->status) == 'pending' ? 'selected' : '' }}>
                        Pending</option>
                    <option value="confirmed" selected
                        {{ old('status',$bayarPinjaman->status) == 'confirmed' ? 'selected' : '' }}>
                        Konfirmasi
                    </option>
                    <option value="rejected" {{ old('status',$bayarPinjaman->status) == 'rejected' ? 'selected' : '' }}
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