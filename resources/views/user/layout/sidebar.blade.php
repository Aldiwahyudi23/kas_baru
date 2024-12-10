<!-- Sidebar user panel (optional) -->
<div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="image">
        <img src="{{asset(Auth::user()->profile_photo_path	)}}" class="img-circle elevation-2" alt="User Image">
    </div>
    <div class="info">
        <a href="#" class="d-block">{{ Auth::user()->name }}</a>
    </div>
</div>

<!-- SidebarSearch Form -->
<div class="form-inline">
    <div class="input-group" data-widget="sidebar-search">
        <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
            <button class="btn btn-sidebar">
                <i class="fas fa-search fa-fw"></i>
            </button>
        </div>
    </div>
</div>

<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
                            with font-awesome or any other icon font library -->
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                    Tentang Perusahaan
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">

                <li class="nav-item">
                    <a href="{{route('kas.pengajuan')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Kas</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('pengeluaran.index')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengeluaran</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('pengeluaran.pengajuan')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Pengeluaran</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('pinjaman.pengajuan')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Pinjaman</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('bayar-pinjaman.pengajuan')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Bayar Pinjaman</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('pinjaman-ke-dua.index')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Pinjaman ke 2</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('setor-tunai.pengajuan')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Setor Tunai</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('income.pengajuan')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Pemasukan Lain</p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a href="{{route('setor-tunai.index')}}" class="nav-link">
                <i class="nav-icon fas fa-th"></i>
                <p>
                    Setor Tunai
                    <span class="right badge badge-danger">New</span>
                </p>
            </a>
        </li>

    </ul>
</nav>
<!-- /.sidebar-menu -->