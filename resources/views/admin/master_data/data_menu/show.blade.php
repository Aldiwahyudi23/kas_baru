@extends('admin.layout.app')

@section('content')

<div class="row">
    <div class="col-md-3">
        <!-- About Me Box -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">{{$DataMenu->name}}</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <strong><i class="fas fa-book mr-1"></i> Icon</strong>
                <p class="text-muted">
                    {{$DataMenu->icon}} <i class="{{$DataMenu->icon}}"></i>
                </p>
                <hr>
                <strong></i> Route / Url</strong>
                <p class="text-muted">
                    {{$DataMenu->routeUrl->name}} ( {{$DataMenu->routeUrl->route_name}} )
                </p>
                <hr>
                <strong><i class="fas fa-clock mr-1"></i> Waktu Input</strong>
                <p class="text-muted">
                    {{$DataMenu->created_at}}
                </p>
                <hr>
                <strong><i class="fas fa-clock mr-1"></i> Terakhir Update</strong>
                <p class="text-muted">
                    {{$DataMenu->updated_at}}
                </p>
                <hr>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <h3 class="profile-username text-center">{{$DataMenu->name}}</h3>
                <p class="text-muted text-center">Data Sub Menu yang terhubung ke Menu</p>
                <ul class="list-group list-group-unbordered mb-3">
                    @foreach($subMenu as $data)
                    <li class="list-group-item">
                        <b>{{$data->name}}</b>
                        @if(Route::has($data->routeUrl->route_name))
                        <a href="{{route('sub-menu.show',Crypt::encrypt($data->id))}}" class="float-right">{{$data->routeUrl->route_name}}</a>
                        @else
                        <span class="badge float-right bg-danger">Route tidak ada</span>
                        @endif

                    </li>
                    @endforeach
                </ul>

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
                    <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Deskripsi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Aktivitas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="activity">
                        <!-- Post -->
                        <div class="post">
                            <div class="user-block">
                                <img class="img-circle img-bordered-sm" src="../../dist/img/user1-128x128.jpg" alt="user image">
                                <span class="username">
                                    <a href="#">Jonathan Burke Jr.</a>
                                    <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                                </span>
                                <span class="description">Shared publicly - 7:30 PM today</span>
                            </div>
                            <!-- /.user-block -->
                            <p>
                                {!!$DataMenu->description!!}
                            </p>
                        </div>
                        <!-- /.post -->
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
                                    {{ $data->created_at->format('Y-m-d') }} <!-- Format sesuai kebutuhan -->
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
                                    <span class="time"><i class="far fa-clock"></i> {{ $data->created_at->format('H:i') }}</span>

                                    <h3 class="timeline-header">{{ $data->admin->name ?? 'Unknown User' }} {{ $data->action }}</h3>

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
                                    <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
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