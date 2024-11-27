@extends('admin.layout.app')

@section('content')

<div class="row">
    <div class="col-md-3">
        <!-- About Me Box -->
        <div class="card card-primary">
            <div class="card-header">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" src="{{asset($dataUser->profile_photo_path)}}"
                        alt="User profile picture">
                </div>
                <h3 class="profile-username text-center">{{$dataUser->name}}</h3>
                <p class="text-muted text-center">{{$dataUser->role->name}}</p>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <strong> Kode</strong>
                <p class="text-muted">
                    {{$dataUser->dataWarga->code}}
                </p>
                <strong> Nama Warga yang terhubung</strong>
                <p class="text-muted">
                    {{$dataUser->dataWarga->name}}
                </p>

                <strong> Waktu Input</strong>
                <p class="text-muted">
                    {{$dataUser->created_at}}
                </p>

                <strong> Terakhir Update</strong>
                <p class="text-muted">
                    {{$dataUser->updated_at}}
                </p>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Status Account</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    {{$dataUser->name}} ({{$dataUser->role->name}})
                    @if ($dataUser->is_active == 1)
                    <span class="badge float-right bg-success">Aktif</span>
                    @else
                    <span class="badge float-right bg-danger">Tidak</span>
                    @endif
                    <br>
                    {{$dataUser->email}}
                    @if ($dataUser->email_verified_at == NULL)
                    <span class="badge float-right bg-danger">Belum di Verifikasi</span>
                    @else
                    <span class="badge float-right bg-success">Terverifikasi</span>
                    @endif
                    <br>
                    {{$dataUser->no_hp}}
                    <a href="http://wa.me/62{{$dataUser->no_hp}}">
                        <span class="badge float-right bg-success"><i class="fas fa-phone mr-1"></i></span>
                    </a>

                </p>
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
                        <div class="card-body">
                            <form action="{{ route('user.update',Crypt::encrypt($dataUser->id)) }}" method="POST"
                                enctype="multipart/form-data" id="adminForm">
                                @method('PATCH')
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="name">Nama <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name"
                                                value="{{old('name',$dataUser->name)}}"
                                                class="form-control col-12 @error('name') is-invalid @enderror">
                                        </div>
                                        <div class="form-group">
                                            <label for="role_id">role_id <span class="text-danger">*</span></label>
                                            <select
                                                class="form-control select2bs4 col-12 @error('role_id') is-invalid @enderror"
                                                name="role_id">
                                                @if(old('role_id',$dataUser->role_id) == true)
                                                <option selected="selected"
                                                    value=" {{old('role_id',$dataUser->role_id)}}">
                                                    {{old('role_id',$dataUser->role->name)}}</option>
                                                @endif
                                                <option value="">--pilih Role--</option>

                                                @foreach($role as $data)
                                                <?php
                                                // Cek apakah role sudah terpakai oleh user lain
                                                $assignedUser = \App\Models\User::where('role_id', $data->id)->first();
                                                ?>

                                                @if(!$assignedUser || !in_array($data->name, ['Ketua', 'Bendahara',
                                                'Sekretaris']))
                                                <option value="{{ $data->id }}" @if($data->is_active == 0) disabled
                                                    @endif>
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
                                        <div class="form-group">
                                            <label for="foto">foto</label>
                                            <input type="file" name="foto" id="foto"
                                                value="{{old('foto',$dataUser->foto)}}"
                                                class="form-control col-12 @error('foto') is-invalid @enderror">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="no_hp">No Handphone /Whatsapp</label>
                                            <input type="text" name="no_hp" id="no_hp"
                                                value="{{old('no_hp',$dataUser->no_hp)}}"
                                                class="form-control col-12 @error('no_hp') is-invalid @enderror">
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" name="email" id="email"
                                                value="{{old('email',$dataUser->email)}}"
                                                class="form-control col-12 @error('email') is-invalid @enderror">
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Reset Kata Sandi</label>
                                            <input type="password" name="password" id="password"
                                                value="{{old('password')}}"
                                                class="form-control col-12 @error('password') is-invalid @enderror">
                                        </div>
                                    </div>
                                </div>

                                <!-- Button Submit -->
                                <button type="submit" class="btn btn-success" id="submitBtns">Create</button>
                            </form>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                                <br>- Bertanda bintang merah wajib di isi.
                            </p>
                        </div>
                        <br>
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Program yang di ikuti</h3>
                            </div>
                            <div class="card-body">

                            </div>
                            <!-- /.card-body -->
                        </div>
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
                                        {{ $data->action }}</h3>

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