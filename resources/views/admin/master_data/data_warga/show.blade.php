@extends('admin.layout.app')

@section('content')

<div class="row">
    <div class="col-md-3">
        <!-- About Me Box -->
        <div class="card card-primary">
            <div class="card-header">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" src="{{asset('storage/'. $dataWarga->foto)}}"
                        alt="User profile picture">
                </div>
                <h3 class="profile-username text-center">{{$dataWarga->name}}</h3>
                <p class="text-muted text-center">Admin</p>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <strong> Kode</strong>
                <p class="text-muted">
                    {{$dataWarga->code}}
                </p>

                <strong> Jenis kelamin</strong>
                <p class="text-muted">
                    {{$dataWarga->jenis_kelamin}}
                </p>

                <strong> Tempat/Tanggal Lahir</strong>
                <p class="text-muted">
                    {{$dataWarga->tempat_lahir}},{{$dataWarga->tanggal_lahir}}
                </p>

                <strong> Agama</strong>
                <p class="text-muted">
                    {{$dataWarga->agama}}
                </p>

                <strong> Alamat</strong>
                <p class="text-muted">
                    {{$dataWarga->alamat}}
                </p>

                <strong> Waktu Input</strong>
                <p class="text-muted">
                    {{$dataWarga->created_at}}
                </p>

                <strong> Terakhir Update</strong>
                <p class="text-muted">
                    {{$dataWarga->updated_at}}
                </p>

                <a class="btn btn-info btn-sm" href="{{route('warga.edit',Crypt::encrypt($dataWarga->id))}}">
                    <i class="fas fa-pencil-alt">
                    </i>
                    Edit Data Warga
                </a>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Account</h3>
                @if ($cek_user == 1)
                <span class="badge float-right bg-success">Aktif</span>
                @else
                <span class="badge float-right bg-danger">Tidak ada Account</span>
                @endif
            </div>
            <div class="card-body">
                @if ($cek_user == 1)
                <p>Account sudah aktif</p>
                <p class="text-muted">
                    {{$user->name}} ({{$user->role->name}})
                    @if ($user->is_active == 1)
                    <span class="badge float-right bg-success">Aktif</span>
                    @else
                    <span class="badge float-right bg-danger">Tidak</span>
                    @endif
                    <br>
                    {{$user->email}}
                    @if ($user->email_verified_at == NULL)
                    <span class="badge float-right bg-danger">Belum di Verifikasi</span>
                    @else
                    <span class="badge float-right bg-success">Terverifikasi</span>
                    @endif
                    <br>
                    {{$user->no_hp}}
                    <a href="http://wa.me/62{{$user->no_hp}}">
                        <span class="badge float-right bg-success"><i class="fas fa-phone mr-1"></i></span>
                    </a>

                </p>
                @else
                <form action="{{ route('account.store') }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @csrf
                    <div class="form-group">
                        <label for="no_hp">No Handphone /Whatsapp</label>
                        <input type="text" name="no_hp" id="no_hp" value="{{old('no_hp', $dataWarga->no_hp)}}"
                            class="form-control col-12 @error('no_hp') is-invalid @enderror">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="{{old('email',$dataWarga->email)}}"
                            class="form-control col-12 @error('email') is-invalid @enderror">
                    </div>
                    <div class="form-group">
                        <label for="role_id">role_id <span class="text-danger">*</span></label>
                        <select class="form-control select2bs4 col-12 @error('role_id') is-invalid @enderror"
                            name="role_id">
                            @if(old('role_id') == true)
                            <option selected="selected" value=" {{old('role_id')}}">{{old('role_id')}}</option>
                            @endif
                            <option value="">--pilih Role--</option>

                            @foreach($role as $data)
                            <?php
                            // Cek apakah role sudah terpakai oleh user lain
                            $assignedUser = \App\Models\User::where('role_id', $data->id)->first();
                            ?>

                            @if(!$assignedUser || !in_array($data->name, ['Ketua', 'Bendahara', 'Sekretaris']))
                            <option value="{{ $data->id }}" @if($data->is_active == 0) disabled @endif>
                                {{ $data->name }}
                                @if($data->is_active == 0) (Tidak Aktif) @endif
                            </option>
                            @else
                            <!-- Jika role sudah diisi, tampilkan opsi dalam keadaan disabled dan tambahkan nama user yang telah mengisinya -->
                            <option value="{{ $data->id }}" disabled>
                                {{ $data->name }} ( Sudah diisi oleh {{ $assignedUser->name }} )
                            </option>
                            @endif
                            @endforeach

                        </select>
                    </div>
                    <input type="hidden" name="name" value="{{$dataWarga->name}}">
                    <input type="hidden" name="warga_id" value="{{$dataWarga->id}}">
                    <input type="hidden" name="foto" value="{{$dataWarga->foto}}">
                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Buat Accoun</button>
                </form>
                @endif
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

    </div>
    <!-- /.col -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Data Lain</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Aktivitas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="activity">
                        <!-- Post -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Status Pernikahan</h3>
                            </div>
                            <?php

                            use App\Models\AccessProgram;
                            use App\Models\DataWarga;

                            ?>
                            @foreach($statusPernikahan as $data)
                            <li class="list-group-item">
                                <b>{{$data->status}}
                                    @if($dataWarga->jenis_kelamin == "Perempuan")

                                    @if($data->warga_suami_id == NULL)

                                    @else
                                    <?php
                                    $warga = DataWarga::Find($data->warga_suami_id);
                                    ?>
                                    dengan {{$warga->name}} pada tanggal
                                    @endif
                                    @endif
                                    @if($dataWarga->jenis_kelamin == "Laki-Laki")
                                    @if($data->warga_istri_id == NULL)

                                    @else
                                    <?php
                                    $warga = DataWarga::Find($data->warga_istri_id);
                                    ?>
                                    dengan {{$warga->name}} pada tanggal
                                    @endif
                                    @endif
                                </b>
                                <span class="badge float-right bg-danger">{{$data->tanggal}}</span>
                            </li>
                            @endforeach
                        </div>

                        <!-- /.card -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Status Pekerjaan</h3>
                            </div>

                            @foreach($statusPekerjaan as $data)
                            <li class="list-group-item">
                                <b>{{$data->status}}</b> <br>
                                {{$data->pekerjaan}}
                                <span class="badge float-right bg-danger">{{$data->created_at}}</span>
                            </li>
                            @endforeach

                        </div>
                        <!-- /.card -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Program yang di ikuti</h3>
                            </div>
                            @foreach($program as $data)
                            <li class="list-group-item">
                                <b>{{ $data->name }}</b>
                                <span class="badge float-right">
                                    <?php
                                    $accessProgram = AccessProgram::where('data_warga_id', $dataWarga->id)->where('program_id', $data->id)->first();
                                    $cekProgram = AccessProgram::where('data_warga_id', $dataWarga->id)->where('program_id', $data->id)->count();
                                    ?>
                                    @if($cekProgram == 1)

                                    <button type="button"
                                        class="btn {{ $accessProgram && $accessProgram->is_active ? 'btn-success' : 'btn-danger' }}"
                                        data-url="{{ route('access_program.toggleStatus', Crypt::encrypt($accessProgram->id)) }}"
                                        data-active="{{ $accessProgram && $accessProgram->is_active ? 1 : 0 }}"
                                        onclick="toggleAccess(this)">
                                        {{ $accessProgram && $accessProgram->is_active ? 'ON' : 'OFF' }}
                                    </button>
                                    @else

                                    <form action="{{ route('programs.toggle') }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="program_id" value="{{ $data->id }}">
                                        <input type="hidden" name="data_warga_id" value="{{ $dataWarga->id }}">
                                        <button type="submit" class="btn btn-primary">Ikuti</button>
                                    </form>
                                    @endif
                                </span>
                            </li>
                            @endforeach


                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="timeline">
                        <!-- The timeline -->
                        <div class="timeline timeline-inverse">
                            @php
                            $previousDate = null; // Variabel untuk menyimpan tanggal sebelumnya
                            @endphp

                            @foreach($activityLogAdmin as $data)
                            <!-- Cek jika created_at berbeda dari yang sebelumnya -->
                            @if ($previousDate !== $data->created_at->toDateString())
                            <!-- timeline time label -->
                            <div class="time-label">
                                <span class="bg-danger">
                                    {{ $data->created_at->format('Y-m-d') }}
                                    <!-- Format sesuai kebutuhan -->
                                </span>
                            </div>
                            <!-- Update previousDate ke tanggal saat ini -->
                            @php
                            $previousDate = $data->created_at->toDateString();
                            @endphp
                            @endif

                            <!-- timeline item -->
                            <div>
                                <i class="fas fa-envelope bg-primary"></i>

                                <div class="timeline-item">
                                    <span class="time"><i class="far fa-clock"></i>
                                        {{ $data->created_at->format('H:i') }}</span>

                                    <h3 class="timeline-header">{{ $data->admin->name ?? 'Unknown User' }}
                                        {{ $data->action }}
                                    </h3>

                                    <div class="timeline-body">
                                        {!! $data->details !!}
                                    </div>
                                </div>
                            </div>
                            <!-- END timeline item -->
                            @endforeach



                            <div>
                                <i class="far fa-clock bg-gray"></i>
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->

                    <div class="tab-pane" id="settings">
                        <form class="form-horizontal">
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="inputName" placeholder="Name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputName2" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="inputName2" placeholder="Name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputExperience" class="col-sm-2 col-form-label">Experience</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="inputExperience"
                                        placeholder="Experience"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputSkills" class="col-sm-2 col-form-label">Skills</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-danger">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div><!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
@endsection