@extends('user.layout.app')

@section('content')

@if ($data->status == "pending")
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-exclamation-triangle"></i> Penting !</h5>
    Harap Konfirmasi dengan benar, pastikan data sesuai karena setelah di konfirmasi maka akan masuk ke data dan akan
    masuk ke perhitungan saldo.
</div>
@endif
@if ($data->status == "Berhasil")
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h5><i class="icon fas fa-check"></i> Terkonfirmasi</h5>
    Data pembayaran kas sudah di konfirmasi, dan sudah masuk ke data.
</div>
@endif
<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">{{$data->product->kategori->name}} {{$data->product->provider->name}} (
            {{number_format($data->product->amount,0,',','.')}} ) <br>
            @if($data->status === 'Selesai')
            <span class="badge badge-success">Selesai</span>
            @elseif($data->status === 'Berhasil')
            <span class="badge badge-info">Berhasil</span>
            @elseif($data->status === 'Proses')
            <span class="badge badge-warning">Proses</span>
            @elseif($data->status === 'Gagal')
            <span class="badge badge-danger">Gagal</span>
            @elseif($data->status === 'pending')
            <span class="badge badge-secondary">Pending</span>
            @else
            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
            @endif
        </h3>
        <div class="card-tools">
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
                        <td>{{$data->code}}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>:</td>
                        <td>{{$data->created_at}}</td>
                    </tr>
                    <tr>
                        <td>Di Input oleh</td>
                        <td>:</td>
                        <td>{{$data->submitted_by}}</td>
                    </tr>

                    <tr>
                        <td>Status / Tempo</td>
                        <td>:</td>
                        <td>
                            @if($data->payment_status === 'Langsung')
                            <span class="badge badge-success">Langsung</span>
                            @elseif($data->payment_status === 'Hutang')
                            <span class="badge badge-info">Hutang</span>
                            @else
                            <span class="badge badge-light">Unknown</span> <!-- default if status is undefined -->
                            @endif
                            <br>
                            {{$data->deadline_date}}
                        </td>
                    </tr>
                    <tr>
                        <td>Pembayaran</td>
                        <td>:</td>
                        <td>{{ $data->payment_method}} <br>
                            @if ($data->is_deposited == true)
                            <span class="badge badge-success"><i class="icon fas fa-check"></i> Done Setor</span>
                            @elseif($data->is_deposited == false)
                            <span class="badge badge-warning"><i class="icon fas fa-exclamation-triangle"></i> Belum di
                                setor</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>No Tujuan</td>
                        <td>:</td>
                        <td>
                            <input type="text" id="tujuan" value=" {{$data->detail->no_hp ?? ''}}"
                                oninput="syncPhoneNumber(this)">
                        </td>
                    </tr>
                    @if ($data->product->kategori->name == "Listrik")
                    <tr>
                        <td>No Listrik</td>
                        <td>:</td>
                        <td>{{$data->detail->no_listrik}}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Atas Nama</td>
                        <td>:</td>
                        <td>{{$data->detail->name}}</td>
                    </tr>
                    <tr>
                        <td>Harga Beli</td>
                        <td>:</td>
                        <td>{{number_format($data->buying_price,0,',','.')}}</td>
                    </tr>
                    <tr>
                        <td>Diskon</td>
                        <td>:</td>
                        <td>{{number_format($data->diskon,0,',','.')}}</td>
                    </tr>
                    <tr>
                        <td>Pembayaran</td>
                        <td>:</td>
                        <td>{{number_format($data->invoice,0,',','.')}}</td>
                    </tr>
                    <tr>
                        <td>Keuntungan</td>
                        <td>:</td>
                        <td>{{number_format($data->margin,0,',','.')}}</td>
                    </tr>
                </tbody>
            </table>
            <div class="card-footer">
                <p>
                    Harga Jual :
                </p>
                <h3> Rp {{number_format($data->price,0,',','.')}} </h3>

                <p>Keterangan : <br>
                    {!!$data->detail->description!!}
                </p>
            </div>
        </div>
        @if ($data->status == "Selesai")
        <a href="{{route('repayment.pulsa',Crypt::encrypt($data->id))}}" class="btn btn-primary col-12 mt-2">Beli Lagi</a>
        @endif
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <p>
            Catatan : <br>
            - Data di atas sudah selesai <br>
            - Pastikan data sesuai <br>
        </p>
        <p><i> Data yang sudah berhasil atau selesai dapat di ajukan kembali agar tidak beberapa kali input</i></p>
    </div>
</div>
<!-- /.card -->


@endsection