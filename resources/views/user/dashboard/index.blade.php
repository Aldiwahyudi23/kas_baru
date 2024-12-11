@extends('user.layout.app')

@section('content')

<div class="container-fluid">
    <!-- Page Heading -->
    <!-- Content Row -->
    <div class="row">
        <!-- Saldo Kas Card -->
        <div class="col-12 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                @livewire('saldo-dashboard-user')
            </div>
        </div>
    </div>

    <!-- Menu Section -->
    <div class="row">
        <!-- Menu Item 1 -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    Setor Tunai
                    <div class="text-white-50 small">Klik untuk mulai</div>
                </div>
                <a href="" class="card-footer text-white clearfix small z-1">
                    <span class="float-left">Lihat Detail</span>
                    <span class="float-right">
                        <i class="fas fa-angle-right"></i>
                    </span>
                </a>
            </div>
        </div>
        <!-- Menu Item 2 -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    Konter
                    <div class="text-white-50 small">konter</div>
                </div>
                <a href="{{Route('konter.index')}}" class="card-footer text-white clearfix small z-1">
                    <span class="float-left">Lihat Detail</span>
                    <span class="float-right">
                        <i class="fas fa-angle-right"></i>
                    </span>
                </a>
            </div>
        </div>
        <!-- Tambahkan menu lainnya dengan cara serupa -->
    </div>

</div>

<!-- Info boxes -->

<div class="row">
    <div class="col-3 col-sm-4 col-md-3">
        <a class="users-list-name" href="{{Route('konter.index')}}">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cog"></i></span>
            </div>
            <p class="text-bold users-list-date" style="font-size:14px;">Konter</p>
        </a>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-3 col-sm-4 col-md-3">
        <a class="users-list-name" href="{{Route('other-income.index')}}">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>
            </div>
            <p class="text-bold users-list-date" style="font-size:14px;">Income Lain</p>
        </a>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix hidden-md-up"></div>

    <div class="col-3 col-sm-4 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-3 col-sm-4 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<div class="row">
    <!-- Total Kas -->
    <div class="col-12">
        <div class="card mb-3 text-center bg-primary text-white shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Total Kas</h5>
                <p class="card-text">Belum Ada Data</p>
            </div>
        </div>
    </div>

    <!-- Pemasukan/Pengeluaran Terakhir -->
    <div class="col-12 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Catatan Terakhir</h5>
                <p><strong>Pemasukan:</strong> Belum Ada Data</p>
                <p><strong>Pengeluaran:</strong> Belum Ada Data</p>
                <a href="#" class="btn btn-outline-primary">Lihat Selengkapnya</a>
            </div>
        </div>
    </div>

    <!-- Uang di Luar -->
    <div class="col-12 mb-3">
        <div class="card bg-warning text-white text-center shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Uang Masih di Luar</h5>
                <p class="card-text">Belum Ada Data</p>
                <a href="#" class="btn btn-light">Lihat Selengkapnya</a>
            </div>
        </div>
    </div>

    <!-- Anggaran -->
    <div class="col-12 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Anggaran</h5>
                <div class="row text-center">
                    <div class="col-6 col-md-3">
                        <p><strong>Kas:</strong> Belum Ada Data</p>
                    </div>
                    <div class="col-6 col-md-3">
                        <p><strong>Pinjaman:</strong> Belum Ada Data</p>
                    </div>
                    <div class="col-6 col-md-3">
                        <p><strong>Darurat:</strong> Belum Ada Data</p>
                    </div>
                    <div class="col-6 col-md-3">
                        <p><strong>Aman:</strong> Belum Ada Data</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Ikon -->
    <div class="col-12">
        <div class="row text-center">
            @php
            $menus = [
            ['icon' => 'fas fa-wallet', 'text' => 'Saldo', 'route' => '#'],
            ['icon' => 'fas fa-money-bill-wave', 'text' => 'Deposite', 'route' => '#'],
            ['icon' => 'fas fa-clipboard-list', 'text' => 'Laporan', 'route' => '#'],
            ['icon' => 'fas fa-cogs', 'text' => 'Pengaturan', 'route' => '#'],
            ];
            @endphp
            @foreach ($menus as $menu)
            <div class="col-6 col-md-3 mb-3">
                <a href="{{ $menu['route'] }}" class="text-decoration-none">
                    <div class="card bg-light shadow-sm">
                        <div class="card-body">
                            <i class="{{ $menu['icon'] }} fa-2x mb-2 text-primary"></i>
                            <p>{{ $menu['text'] }}</p>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

<style>
@media (max-width: 576px) {
    .card-body i {
        font-size: 1.5rem;
    }

    .card-body p {
        font-size: 0.9rem;
    }

    .card {
        margin-bottom: 1rem;
    }

    .card:hover {
        transform: scale(1.02);
        transition: transform 0.2s ease-in-out;
    }

}
</style>