@extends('admin.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-12">
        <h4>{{$data->type}} ( {{$data->name}} )</h4>
        <!-- Data ini di ambil dari file terpisah view/admin/master_data/data_admin/tabel -->
        @livewire('access-notif.kas-payment', ['id' => $data->id])

        <!-- /.card -->
        <div class="card-footer">
            Keterangan:
            <p>
                {!!$data->keterangan!!}
            </p>
        </div>
    </div>
</div>
@endsection

@section('script')


@endsection