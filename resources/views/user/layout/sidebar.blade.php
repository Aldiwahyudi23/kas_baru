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
<?php

use App\Models\CashExpenditures;
use App\Models\Deposit;
use App\Models\KasPayment;
use App\Models\Konter\TransaksiKonter;
use App\Models\Loan;
use App\Models\LoanExtension;
use App\Models\loanRepayment;
use App\Models\OtherIncomes;

$kas = KasPayment::whereIn('status', ['pending','process'])->get();
$loan = Loan::whereIn('status', ['pending', 'approved_by_chairman', 'disbursed_by_treasurer'])->get();
$ex = CashExpenditures::whereIn('status', ['approved_by_chairman', 'disbursed_by_treasurer'])->get();
$rePayment = loanRepayment::whereIn('status', ['pending','process'])->get();
$konter = TransaksiKonter::where('status', 'Proses')->get();
$income = OtherIncomes::whereIn('status', ['pending','process'])->get();
$loan2 = LoanExtension::where('status', 'pending')->get();
$deposit = Deposit::where('status', 'pending')->get();

$total = $kas->count() + $loan->count() + $ex->count() + $rePayment->count() + $income->count() + $loan2->count() + $deposit->count();
?>
<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
                            with font-awesome or any other icon font library -->
        @if(in_array(Auth::user()->role->name , ['Bendahara' , 'Wakil Bendahara' , 'Sekretaris' , 'Wakil
        Sekretaris'
        , 'Ketua' , 'Wakil Ketua']))
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                    Tentang Perusahaan
                    @if ($total >= 1)
                    <span class="right badge badge-danger">{{$total}}</span>
                    @endif
                    <i class="right fas fa-angle-left">
                    </i>
                </p>
            </a>
            <ul class="nav nav-treeview">

                <li class="nav-item">
                    <a href="{{route('kas.pengajuan')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Kas
                            @if ($kas->count() >= 1)
                            <span class="right badge badge-danger">{{$kas->count()}}</span>
                            @endif
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('pengeluaran.index')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengeluaran

                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('pengeluaran.pengajuan')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Pengeluaran
                            @if ($ex->count() >= 1)
                            <span class="right badge badge-danger">{{$ex->count()}}</span>
                            @endif
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('pinjaman.pengajuan')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Pinjaman
                            @if ($loan->count() >= 1)
                            <span class="right badge badge-danger">{{$loan->count()}}</span>
                            @endif
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('bayar-pinjaman.pengajuan')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Bayar Pinjaman
                            @if ($rePayment->count() >= 1)
                            <span class="right badge badge-danger">{{$rePayment->count()}}</span>
                            @endif
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('pinjaman-ke-dua.index')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Pinjaman ke 2
                            @if ($loan2->count() >= 1)
                            <span class="right badge badge-danger">{{$loan2->count()}}</span>
                            @endif
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('setor-tunai.pengajuan')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Setor Tunai
                            @if ($deposit->count() >= 1)
                            <span class="right badge badge-danger">{{$deposit->count()}}</span>
                            @endif
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('income.pengajuan')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Pengajuan Pemasukan Lain
                            @if ($income->count() >= 1)
                            <span class="right badge badge-danger">{{$income->count()}}</span>
                            @endif
                        </p>
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

        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                    Member
                    <i class="right fas fa-angle-left">
                    </i>
                </p>
            </a>
            <ul class="nav nav-treeview">

                <li class="nav-item">
                    <a href="{{route('member-types.index')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Type Member</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('members.index')}}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Data Member
                        </p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

    </ul>
</nav>
<!-- /.sidebar-menu -->