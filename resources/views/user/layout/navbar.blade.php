            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('user.dashboard') }}" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{route('kas.index')}}" class="nav-link">Bayar</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{route('tentang.index')}}" class="nav-link">profile</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{route('pinjaman.index')}}" class="nav-link">Pinjam</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{route('pinjaman.index')}}" class="nav-link">Setting</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Navbar Search -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                        <i class="fas fa-search"></i>
                    </a>
                    <div class="navbar-search-block">
                        <form class="form-inline">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-navbar" type="search" placeholder="Search"
                                    aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-navbar" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>

                <!-- Messages Dropdown Menu -->
                <?php

                use App\Models\CashExpenditures;
                use App\Models\Deposit;
                use App\Models\KasPayment;
                use App\Models\Konter\TransaksiKonter;
                use App\Models\Loan;
                use App\Models\LoanExtension;
                use App\Models\loanRepayment;
                use App\Models\OtherIncomes;

                $kas = KasPayment::where('status', 'process')->get();
                $loan = Loan::whereIn('status', ['pending', 'approved_by_chairman', 'disbursed_by_treasurer'])->get();
                $ex = CashExpenditures::whereIn('status', ['approved_by_chairman', 'disbursed_by_treasurer'])->get();
                $rePayment = loanRepayment::where('status', 'process')->get();
                $konter = TransaksiKonter::where('status', 'Proses')->get();
                $income = OtherIncomes::where('status', 'process')->get();
                $loan2 = LoanExtension::where('status', 'pending')->get();
                $deposit = Deposit::where('status', 'pending')->get();

                $total = $kas->count() + $loan->count() + $ex->count() + $rePayment->count() + $konter->count() + $income->count() + $loan2->count() + $deposit->count();
                ?>

                @if(in_array(Auth::user()->role->name , ['Bendahara' , 'Wakil Bendahara' , 'Sekretaris' , 'Wakil
                Sekretaris'
                , 'Ketua' , 'Wakil Ketua']))
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        @if ($total >= 1)
                        <span class="badge badge-danger navbar-badge">{{$total}}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                        @foreach ($kas as $data )
                        <a href="{{ route('kas.show.confirm',Crypt::encrypt($data->id)) }}" class="dropdown-item">
                            <!-- Message Start -->
                            <div class="media">
                                <img src="{{asset($data->data_warga->foto)}}" alt="User Avatar"
                                    class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Kas
                                    </h3>
                                    <p class="text-sm">{{$data->data_warga->name}}
                                        {{number_format($data->amount,0,',','.')}}
                                    </p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>
                                        {{Carbon\Carbon::parse($data->created_at)->diffForHumans()}}
                                    </p>
                                </div>
                            </div>
                            <!-- Message End -->
                        </a>
                        @endforeach
                        @foreach ($loan as $data )
                        <a href="{{ route('pinjaman.show.confirm',Crypt::encrypt($data->id)) }}" class="dropdown-item">
                            <!-- Message Start -->
                            <div class="media">
                                <img src="{{asset($data->warga->foto)}}" alt="User Avatar"
                                    class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Pinjaman
                                    </h3>
                                    <p class="text-sm">{{$data->warga->name}}
                                        {{number_format($data->loan_amount,0,',','.')}}
                                    </p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>
                                        {{Carbon\Carbon::parse($data->created_at)->diffForHumans()}}
                                    </p>
                                </div>
                            </div>
                            <!-- Message End -->
                        </a>
                        @endforeach
                        @foreach ($ex as $data )
                        <a href="{{ route('pengeluaran.show.confirm',Crypt::encrypt($data->id)) }}"
                            class="dropdown-item">
                            <!-- Message Start -->
                            <div class="media">
                                <img src="{{asset($data->sekretaris->foto)}}" alt="User Avatar"
                                    class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Pengeluaran
                                    </h3>
                                    <p class="text-sm">{{$data->sekretaris->name}}
                                        {{number_format($data->amount,0,',','.')}}
                                    </p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>
                                        {{Carbon\Carbon::parse($data->created_at)->diffForHumans()}}
                                    </p>
                                </div>
                            </div>
                            <!-- Message End -->
                        </a>
                        @endforeach
                        @foreach ($rePayment as $data )
                        <a href="{{ route('bayar-pinjaman.show.confirm',Crypt::encrypt($data->id)) }}"
                            class="dropdown-item">
                            <!-- Message Start -->
                            <div class="media">
                                <img src="{{asset($data->data_warga->foto)}}" alt="User Avatar"
                                    class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Bayar Pinjaman
                                    </h3>
                                    <p class="text-sm">{{$data->data_warga->name}}
                                        {{number_format($data->amount,0,',','.')}}
                                    </p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>
                                        {{Carbon\Carbon::parse($data->created_at)->diffForHumans()}}
                                    </p>
                                </div>
                            </div>
                            <!-- Message End -->
                        </a>
                        @endforeach
                        @foreach ($konter as $data )
                        <a href="{{ route('konter.pengajuan',Crypt::encrypt($data->id)) }}" class="dropdown-item">
                            <!-- Message Start -->
                            <div class="media">
                                <img src="as" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        {{$data->product->kategori->name}} ({{$data->product->provider->name}})
                                    </h3>
                                    <p class="text-sm">{{$data->submitted_by}}
                                        {{number_format($data->product->amount,0,',','.')}}
                                    </p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>
                                        {{Carbon\Carbon::parse($data->created_at)->diffForHumans()}}
                                    </p>
                                </div>
                            </div>
                            <!-- Message End -->
                        </a>
                        @endforeach
                        @foreach ($income as $data )
                        <a href="{{ route('income.show.confirm',Crypt::encrypt($data->id)) }}" class="dropdown-item">
                            <!-- Message Start -->
                            <div class="media">
                                <img src="{{asset($data->submitted->foto)}}" alt="User Avatar"
                                    class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Income
                                    </h3>
                                    <p class="text-sm">{{$data->submitted->name}}
                                        {{number_format($data->amount,0,',','.')}}
                                    </p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>
                                        {{Carbon\Carbon::parse($data->created_at)->diffForHumans()}}
                                    </p>
                                </div>
                            </div>
                            <!-- Message End -->
                        </a>
                        @endforeach
                        @foreach ($loan2 as $data )
                        <a href="{{ route('pinjaman-ke-dua.show.confirm',Crypt::encrypt($data->id)) }}"
                            class="dropdown-item">
                            <!-- Message Start -->
                            <div class="media">
                                <img src="{{asset($data->data_warga->foto)}}" alt="User Avatar"
                                    class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Pinjaman ke Dua
                                    </h3>
                                    <p class="text-sm">{{$data->data_warga->name}}
                                    </p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>
                                        {{Carbon\Carbon::parse($data->created_at)->diffForHumans()}}
                                    </p>
                                </div>
                            </div>
                            <!-- Message End -->
                        </a>
                        @endforeach
                        @foreach ($deposit as $data )
                        <a href="{{ route('setor-tunai.show.confirm',Crypt::encrypt($data->id)) }}"
                            class="dropdown-item">
                            <!-- Message Start -->
                            <div class="media">
                                <img src="{{asset($data->submit->foto)}}" alt="User Avatar"
                                    class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Setor Tunai
                                    </h3>
                                    <p class="text-sm">{{$data->submit->name}}
                                        {{number_format($data->amount,0,',','.')}}
                                    </p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>
                                        {{Carbon\Carbon::parse($data->created_at)->diffForHumans()}}
                                    </p>
                                </div>
                            </div>
                            <!-- Message End -->
                        </a>
                        @endforeach
                    </div>
                </li>
                @endif



                <!-- Notifications Dropdown Menu -->

                <!-- <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li> -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown4" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown4">
                        <a class="dropdown-item" href="user/profile">Profile</a>
                        <a class="dropdown-item" href="#">Support</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>

                    </div>
                </li>
            </ul>