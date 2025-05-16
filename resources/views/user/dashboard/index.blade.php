@extends('user.layout.app')

@section('content')
<!-- Info boxes -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Saldo KAS</h5>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-wrench"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a href="#" class="dropdown-item">Action</a>
                            <a href="#" class="dropdown-item">Another action</a>
                            <a href="#" class="dropdown-item">Something else here</a>
                            <a class="dropdown-divider"></a>
                            <a href="#" class="dropdown-item">Separated link</a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <h3> <B>
                            Rp {{number_format( $saldo->total_balance ?? 0,0,',','.')}}
                        </B></h3>
                    <div class="text-white-50 small"> Rp
                        {{number_format( $saldo->amount ?? 0,0,',','.')}} ( {{$saldo->code ?? ''}} )
                    </div>
                </div>
                <a href="{{Route('dashboard.saldo')}}" class="card-footer text-white clearfix small z-1">
                    <span class="float-left">Lihat Detail</span>
                    <span class="float-right">
                        <i class="fas fa-angle-right"></i>
                    </span>
                </a>
            </div>
            @if(Auth::user()->role->name == "Bendahara" || Auth::user()->role->name == "Wakil Bendahara" ||
            Auth::user()->role->name == "Sekretaris" || Auth::user()->role->name == "Wakil Sekretaris" ||
            Auth::user()->role->name == "Ketua" || Auth::user()->role->name == "Wakil Ketua")

            @if ($saldo->cash_outside > 0)
            <div class="card bg-primary text-white shadow">
                <center>
                    <h5>
                        Saldo Belum di TF <br>
                        Rp {{number_format( $saldo->cash_outside ?? 0,0,',','.')}}
                    </h5>
                </center>
                <a href="{{route('setor-tunai.index')}}" class="card-footer text-white clearfix small z-1">
                    <span class="float-left">Lihat Detail</span>
                    <span class="float-right">
                        <i class="fas fa-angle-right"></i>
                    </span>
                </a>
            </div>
            <!-- /.row -->
            @endif

            @endif
            @if ($total > 0)
            @include('user.dashboard.tagihan')
            @endif


            <!-- ./card-body -->
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-3 col-6">
                        <a href="{{Route('saldo.anggaran', ['type' => 'Dana Kas'])}}"
                            class="text-white no-underline hover:text-gray-300">
                            <div class="description-block border-right">
                                <h5 class="description-header">Rp {{number_format( $saldo_kas->saldo ?? 0,0,',','.')}}
                                </h5>
                                <span class="description-text">DANA KAS</span>
                            </div>
                        </a>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                        <a href="{{Route('saldo.anggaran', ['type' => 'Dana Amal'])}}"
                            class="text-white no-underline hover:text-gray-300">
                            <div class="description-block border-right">
                                <h5 class="description-header">Rp {{number_format( $saldo_amal->saldo ?? 0,0,',','.')}}
                                </h5>
                                <span class="description-text">DANA AMAL</span>
                            </div>
                        </a>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                        <a href="{{Route('saldo.anggaran', ['type' => 'Dana Darurat'])}}"
                            class="text-white no-underline hover:text-gray-300">
                            <div class="description-block border-right">
                                <h5 class="description-header">Rp
                                    {{number_format( $saldo_darurat->saldo ?? 0,0,',','.')}}
                                </h5>
                                <span class="description-text">DANA DARURAT</span>
                            </div>
                        </a>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                        <a href="{{Route('saldo.anggaran', ['type' => 'Dana Pinjam'])}}"
                            class="text-white no-underline hover:text-gray-300">
                            <div class="description-block">
                                <h5 class="description-header">Rp
                                    {{number_format( $saldo_pinjam->saldo ?? 0,0,',','.')}}
                                </h5>
                                <span class="description-text">DANA PINJAM</span>
                            </div>
                        </a>
                        <!-- /.description-block -->
                    </div>
                    <div class="col-sm-3 col-6">
                        <a href="{{Route('saldo.anggaran', ['type' => 'Dana Acara'])}}"
                            class="text-white no-underline hover:text-gray-300">
                            <div class="description-block">
                                <h5 class="description-header">Rp
                                    {{number_format( $saldo_acara->saldo ?? 0,0,',','.')}}
                                </h5>
                                <span class="description-text">DANA ACARA</span>
                            </div>
                        </a>
                        <!-- /.description-block -->
                    </div>
                    <div class="col-sm-3 col-6">
                        <a href="{{Route('saldo.anggaran', ['type' => 'Dana Usaha'])}}"
                            class="text-white no-underline hover:text-gray-300">
                            <div class="description-block">
                                <h5 class="description-header">Rp
                                    {{number_format( $saldo_usaha->saldo ?? 0,0,',','.')}}
                                </h5>
                                <span class="description-text">DANA USAHA</span>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
<div class="row">
    @if($isMemberKonter || $isPengurus)
    <div class="col-3 col-sm-4 col-md-3">
        <a class="users-list-name" href="{{Route('konter.index')}}">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cog"></i></span>
            </div>
            <p class="text-bold users-list-date" style="font-size:14px;">Konter</p>
        </a>
        <!-- /.info-box -->
    </div>
    @endif

    @if($isPengurus)

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
        <a class="users-list-name" href="{{route('pengeluaran.index')}}">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
            </div>
            <p class="text-bold users-list-date" style="font-size:14px;">Pengeluaran</p>
        </a>
        <!-- /.info-box -->
    </div>
    <div class="clearfix hidden-md-up"></div>

    <div class="col-3 col-sm-4 col-md-3">
        <a class="users-list-name" href="{{route('pinjaman.laporan')}}">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
            </div>
            <p class="text-bold users-list-date" style="font-size:14px;">L Pinjaman</p>
        </a>
        <!-- /.info-box -->
    </div>
    <div class="clearfix hidden-md-up"></div>
    @endif
    <div class="col-3 col-sm-4 col-md-3">
        <a class="users-list-name" href="{{route('pulsaUser')}}">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
            </div>
            <p class="text-bold users-list-date" style="font-size:14px;">Pulsa</p>
        </a>
        <!-- /.info-box -->
    </div>
    <div class="clearfix hidden-md-up"></div>

    <div class="col-3 col-sm-4 col-md-3">
        <a class="users-list-name" href="{{route('token_listrikUser')}}">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
            </div>
            <p class="text-bold users-list-date" style="font-size:14px;">Token Listrik</p>
        </a>
        <!-- /.info-box -->
    </div>
    <div class="clearfix hidden-md-up"></div>

    <div class="col-3 col-sm-4 col-md-3">
        <a class="users-list-name" href="{{route('tagihan_listrikUser')}}">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
            </div>
            <p class="text-bold users-list-date" style="font-size:14px;">Tagihan Listrik</p>
        </a>
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

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <div class="col-md-8">
        <!-- MAP & BOX PANE -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">US-Visitors Report</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <div class="d-md-flex">
                    <div class="p-1 flex-fill" style="overflow: hidden">
                        <!-- Map will be created here -->
                        <div id="world-map-markers" style="height: 325px; overflow: hidden">
                            <div class="map"></div>
                        </div>
                    </div>
                    <div class="card-pane-right bg-success pt-2 pb-2 pl-4 pr-4">
                        <div class="description-block mb-4">
                            <div class="sparkbar pad" data-color="#fff">90,70,90,70,75,80,70</div>
                            <h5 class="description-header">8390</h5>
                            <span class="description-text">Visits</span>
                        </div>
                        <!-- /.description-block -->
                        <div class="description-block mb-4">
                            <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
                            <h5 class="description-header">30%</h5>
                            <span class="description-text">Referrals</span>
                        </div>
                        <!-- /.description-block -->
                        <div class="description-block">
                            <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
                            <h5 class="description-header">70%</h5>
                            <span class="description-text">Organic</span>
                        </div>
                        <!-- /.description-block -->
                    </div><!-- /.card-pane-right -->
                </div><!-- /.d-md-flex -->
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
        <div class="row">
            <div class="col-md-6">
                <!-- DIRECT CHAT -->
                <div class="card direct-chat direct-chat-warning">
                    <div class="card-header">
                        <h3 class="card-title">Direct Chat</h3>

                        <div class="card-tools">
                            <span title="3 New Messages" class="badge badge-warning">3</span>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" title="Contacts" data-widget="chat-pane-toggle">
                                <i class="fas fa-comments"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Conversations are loaded here -->
                        <div class="direct-chat-messages">
                            <!-- Message. Default to the left -->
                            <div class="direct-chat-msg">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-left">Alexander Pierce</span>
                                    <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                                </div>
                                <!-- /.direct-chat-infos -->
                                <img class="direct-chat-img" src="dist/img/user1-128x128.jpg" alt="message user image">
                                <!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    Is this template really for free? That's unbelievable!
                                </div>
                                <!-- /.direct-chat-text -->
                            </div>
                            <!-- /.direct-chat-msg -->

                            <!-- Message to the right -->
                            <div class="direct-chat-msg right">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-right">Sarah Bullock</span>
                                    <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                                </div>
                                <!-- /.direct-chat-infos -->
                                <img class="direct-chat-img" src="dist/img/user3-128x128.jpg" alt="message user image">
                                <!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    You better believe it!
                                </div>
                                <!-- /.direct-chat-text -->
                            </div>
                            <!-- /.direct-chat-msg -->

                            <!-- Message. Default to the left -->
                            <div class="direct-chat-msg">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-left">Alexander Pierce</span>
                                    <span class="direct-chat-timestamp float-right">23 Jan 5:37 pm</span>
                                </div>
                                <!-- /.direct-chat-infos -->
                                <img class="direct-chat-img" src="dist/img/user1-128x128.jpg" alt="message user image">
                                <!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    Working with AdminLTE on a great new app! Wanna join?
                                </div>
                                <!-- /.direct-chat-text -->
                            </div>
                            <!-- /.direct-chat-msg -->

                            <!-- Message to the right -->
                            <div class="direct-chat-msg right">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-right">Sarah Bullock</span>
                                    <span class="direct-chat-timestamp float-left">23 Jan 6:10 pm</span>
                                </div>
                                <!-- /.direct-chat-infos -->
                                <img class="direct-chat-img" src="dist/img/user3-128x128.jpg" alt="message user image">
                                <!-- /.direct-chat-img -->
                                <div class="direct-chat-text">
                                    I would love to.
                                </div>
                                <!-- /.direct-chat-text -->
                            </div>
                            <!-- /.direct-chat-msg -->

                        </div>
                        <!--/.direct-chat-messages-->

                        <!-- Contacts are loaded here -->
                        <div class="direct-chat-contacts">
                            <ul class="contacts-list">
                                <li>
                                    <a href="#">
                                        <img class="contacts-list-img" src="dist/img/user1-128x128.jpg"
                                            alt="User Avatar">

                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                                Count Dracula
                                                <small class="contacts-list-date float-right">2/28/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">How have you been? I was...</span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>
                                <!-- End Contact Item -->
                                <li>
                                    <a href="#">
                                        <img class="contacts-list-img" src="dist/img/user7-128x128.jpg"
                                            alt="User Avatar">

                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                                Sarah Doe
                                                <small class="contacts-list-date float-right">2/23/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">I will be waiting for...</span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>
                                <!-- End Contact Item -->
                                <li>
                                    <a href="#">
                                        <img class="contacts-list-img" src="dist/img/user3-128x128.jpg"
                                            alt="User Avatar">

                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                                Nadia Jolie
                                                <small class="contacts-list-date float-right">2/20/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">I'll call you back at...</span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>
                                <!-- End Contact Item -->
                                <li>
                                    <a href="#">
                                        <img class="contacts-list-img" src="dist/img/user5-128x128.jpg"
                                            alt="User Avatar">

                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                                Nora S. Vans
                                                <small class="contacts-list-date float-right">2/10/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">Where is your new...</span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>
                                <!-- End Contact Item -->
                                <li>
                                    <a href="#">
                                        <img class="contacts-list-img" src="dist/img/user6-128x128.jpg"
                                            alt="User Avatar">

                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                                John K.
                                                <small class="contacts-list-date float-right">1/27/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">Can I take a look at...</span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>
                                <!-- End Contact Item -->
                                <li>
                                    <a href="#">
                                        <img class="contacts-list-img" src="dist/img/user8-128x128.jpg"
                                            alt="User Avatar">

                                        <div class="contacts-list-info">
                                            <span class="contacts-list-name">
                                                Kenneth M.
                                                <small class="contacts-list-date float-right">1/4/2015</small>
                                            </span>
                                            <span class="contacts-list-msg">Never mind I found...</span>
                                        </div>
                                        <!-- /.contacts-list-info -->
                                    </a>
                                </li>
                                <!-- End Contact Item -->
                            </ul>
                            <!-- /.contacts-list -->
                        </div>
                        <!-- /.direct-chat-pane -->
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <form action="#" method="post">
                            <div class="input-group">
                                <input type="text" name="message" placeholder="Type Message ..." class="form-control">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-warning">Send</button>
                                </span>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-footer-->
                </div>
                <!--/.direct-chat -->
            </div>
            <!-- /.col -->

            <div class="col-md-6">
                <!-- USERS LIST -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Latest Members</h3>

                        <div class="card-tools">
                            <span class="badge badge-danger">8 New Members</span>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                        <ul class="users-list clearfix">
                            <li>
                                <img src="dist/img/user1-128x128.jpg" alt="User Image">
                                <a class="users-list-name" href="#">Alexander Pierce</a>
                                <span class="users-list-date">Today</span>
                            </li>
                            <li>
                                <img src="dist/img/user8-128x128.jpg" alt="User Image">
                                <a class="users-list-name" href="#">Norman</a>
                                <span class="users-list-date">Yesterday</span>
                            </li>
                            <li>
                                <img src="dist/img/user7-128x128.jpg" alt="User Image">
                                <a class="users-list-name" href="#">Jane</a>
                                <span class="users-list-date">12 Jan</span>
                            </li>
                            <li>
                                <img src="dist/img/user6-128x128.jpg" alt="User Image">
                                <a class="users-list-name" href="#">John</a>
                                <span class="users-list-date">12 Jan</span>
                            </li>
                            <li>
                                <img src="dist/img/user2-160x160.jpg" alt="User Image">
                                <a class="users-list-name" href="#">Alexander</a>
                                <span class="users-list-date">13 Jan</span>
                            </li>
                            <li>
                                <img src="dist/img/user5-128x128.jpg" alt="User Image">
                                <a class="users-list-name" href="#">Sarah</a>
                                <span class="users-list-date">14 Jan</span>
                            </li>
                            <li>
                                <img src="dist/img/user4-128x128.jpg" alt="User Image">
                                <a class="users-list-name" href="#">Nora</a>
                                <span class="users-list-date">15 Jan</span>
                            </li>
                            <li>
                                <img src="dist/img/user3-128x128.jpg" alt="User Image">
                                <a class="users-list-name" href="#">Nadia</a>
                                <span class="users-list-date">15 Jan</span>
                            </li>
                        </ul>
                        <!-- /.users-list -->
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer text-center">
                        <a href="javascript:">View All Users</a>
                    </div>
                    <!-- /.card-footer -->
                </div>
                <!--/.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- TABLE: LATEST ORDERS -->
        <div class="card">
            <div class="card-header border-transparent">
                <h3 class="card-title">Latest Orders</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Item</th>
                                <th>Status</th>
                                <th>Popularity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR9842</a></td>
                                <td>Call of Duty IV</td>
                                <td><span class="badge badge-success">Shipped</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR1848</a></td>
                                <td>Samsung Smart TV</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR7429</a></td>
                                <td>iPhone 6 Plus</td>
                                <td><span class="badge badge-danger">Delivered</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR7429</a></td>
                                <td>Samsung Smart TV</td>
                                <td><span class="badge badge-info">Processing</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#00c0ef" data-height="20">90,80,-90,70,-61,83,63
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR1848</a></td>
                                <td>Samsung Smart TV</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR7429</a></td>
                                <td>iPhone 6 Plus</td>
                                <td><span class="badge badge-danger">Delivered</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="pages/examples/invoice.html">OR9842</a></td>
                                <td>Call of Duty IV</td>
                                <td><span class="badge badge-success">Shipped</span></td>
                                <td>
                                    <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                <a href="javascript:void(0)" class="btn btn-sm btn-info float-left">Place New Order</a>
                <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-right">View All Orders</a>
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->

    <div class="col-md-4">
        <!-- Info Boxes Style 2 -->
        <div class="info-box mb-3 bg-warning">
            <span class="info-box-icon"><i class="fas fa-tag"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Inventory</span>
                <span class="info-box-number">5,200</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 bg-success">
            <span class="info-box-icon"><i class="far fa-heart"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Mentions</span>
                <span class="info-box-number">92,050</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 bg-danger">
            <span class="info-box-icon"><i class="fas fa-cloud-download-alt"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Downloads</span>
                <span class="info-box-number">114,381</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box mb-3 bg-info">
            <span class="info-box-icon"><i class="far fa-comment"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Direct Messages</span>
                <span class="info-box-number">163,921</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Browser Usage</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="chart-responsive">
                            <canvas id="pieChart" height="150"></canvas>
                        </div>
                        <!-- ./chart-responsive -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-4">
                        <ul class="chart-legend clearfix">
                            <li><i class="far fa-circle text-danger"></i> Chrome</li>
                            <li><i class="far fa-circle text-success"></i> IE</li>
                            <li><i class="far fa-circle text-warning"></i> FireFox</li>
                            <li><i class="far fa-circle text-info"></i> Safari</li>
                            <li><i class="far fa-circle text-primary"></i> Opera</li>
                            <li><i class="far fa-circle text-secondary"></i> Navigator</li>
                        </ul>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer p-0">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            United States of America
                            <span class="float-right text-danger">
                                <i class="fas fa-arrow-down text-sm"></i>
                                12%</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            India
                            <span class="float-right text-success">
                                <i class="fas fa-arrow-up text-sm"></i> 4%
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            China
                            <span class="float-right text-warning">
                                <i class="fas fa-arrow-left text-sm"></i> 0%
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- /.footer -->
        </div>
        <!-- /.card -->

        <!-- PRODUCT LIST -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recently Added Products</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    <li class="item">
                        <div class="product-img">
                            <img src="dist/img/default-150x150.png" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">Samsung TV
                                <span class="badge badge-warning float-right">$1800</span></a>
                            <span class="product-description">
                                Samsung 32" 1080p 60Hz LED Smart HDTV.
                            </span>
                        </div>
                    </li>
                    <!-- /.item -->
                    <li class="item">
                        <div class="product-img">
                            <img src="dist/img/default-150x150.png" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">Bicycle
                                <span class="badge badge-info float-right">$700</span></a>
                            <span class="product-description">
                                26" Mongoose Dolomite Men's 7-speed, Navy Blue.
                            </span>
                        </div>
                    </li>
                    <!-- /.item -->
                    <li class="item">
                        <div class="product-img">
                            <img src="dist/img/default-150x150.png" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">
                                Xbox One <span class="badge badge-danger float-right">
                                    $350
                                </span>
                            </a>
                            <span class="product-description">
                                Xbox One Console Bundle with Halo Master Chief Collection.
                            </span>
                        </div>
                    </li>
                    <!-- /.item -->
                    <li class="item">
                        <div class="product-img">
                            <img src="dist/img/default-150x150.png" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">PlayStation 4
                                <span class="badge badge-success float-right">$399</span></a>
                            <span class="product-description">
                                PlayStation 4 500GB Console (PS4)
                            </span>
                        </div>
                    </li>
                    <!-- /.item -->
                </ul>
            </div>
            <!-- /.card-body -->
            <div class="card-footer text-center">
                <a href="javascript:void(0)" class="uppercase">View All Products</a>
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
@endsection