@extends('admin.layout.app')

@section('content')

<div class="row">
    <div class="col-md-3">
        <!-- About Me Box -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class=" text-center">{{$DataAnggaran->name}}</h3>
                <p class="text-muted text-center">{{$DataAnggaran->code}}</p>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <strong> Status</strong>
                @if($DataAnggaran->is_active == 1)
                <p class="text-muted">
                    Aktif
                </p>
                @else
                <p class="text-muted">
                    Tidak Aktif
                </p>
                @endif
                <hr>
                <strong>Kode Anggaran</strong>
                <p class="text-muted">
                    {{$DataAnggaran->code_anggaran}}
                </p>
                <hr>
                <hr>
                <strong><i class="fas fa-clock mr-1"></i> Waktu Input</strong>
                <p class="text-muted">
                    {{$DataAnggaran->created_at}}
                </p>
                <hr>
                <strong><i class="fas fa-clock mr-1"></i> Terakhir Update</strong>
                <p class="text-muted">
                    {{$DataAnggaran->updated_at}}
                </p>
                <hr>
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
                    <li class="nav-item"><a class="nav-link active" href="#descrip" data-toggle="tab">Deskripsi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Aktivitas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="descrip">
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
                                {!!$DataAnggaran->description!!}
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
                        <form id="anggaran-form" class="form-horizontal">
                            <div class="form-group row">
                                <label for="label_anggaran" class="col-sm-2 col-form-label">Label</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="label_anggaran" id="label_anggaran" placeholder="Tambah Label anggaran">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="catatan_anggaran" class="col-sm-2 col-form-label">Catatan</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="catatan_anggaran" name="catatan_anggaran" placeholder="Catatan"></textarea>
                                </div>
                            </div>
                            <input type="hidden" name="anggaran_id" value="{{$DataAnggaran->id}}">
                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-danger">Simpan</button>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <table id="anggaran-list" class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Label</th>
                                    <th>Catatan anggaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 0; ?>
                                @foreach($anggaran_sett as $data)
                                <?php $no++; ?>
                                <tr id="row-{{$data->id}}">
                                    <td>{{$no}} </td>
                                    <td>{{$data->label_anggaran}} </td>
                                    <td>{{$data->catatan_anggaran}} </td>
                                    <td class="project-actions text-right">
                                        <button class="btn btn-info btn-sm edit-anggaran" data-url="{{ route('anggaran_setting_update', Crypt::encrypt($data->id)) }}" data-id="{{ $data->id }}" data-label="{{ $data->label_anggaran }}" data-catatan="{{ $data->catatan_anggaran }}">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </button>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Fungsi untuk menambahkan anggaran baru
    $('#anggaran-form').on('submit', function(event) {
        event.preventDefault(); // Mencegah refresh form saat submit

        $.ajax({
            url: "{{ route('anggaran-setting_store') }}", // URL tujuan request
            method: "POST", // Metode HTTP
            data: $(this).serialize(), // Mengirimkan data form
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}" // Token CSRF untuk Laravel
            },
            success: function(response) {
                if (response.success) {
                    // Tampilkan alert SweetAlert jika berhasil
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan',
                    });

                    // Tambahkan data baru ke dalam tabel secara real-time
                    let newData = `<tr id="row-${response.anggaran.id}">
                                    <td>${$('#anggaran-list tbody tr').length + 1}</td>
                                    <td>${response.anggaran.label_anggaran}</td>
                                    <td>${response.anggaran.catatan_anggaran}</td>
                                    <td class="project-actions text-right">
                                        <button class="btn btn-danger btn-sm delete-anggaran" data-id="${response.anggaran.id}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                  </tr>`;
                    $('#anggaran-list tbody').append(newData);

                    // Reset form setelah submit
                    $('#anggaran-form')[0].reset();
                } else {
                    // Tampilkan alert jika gagal
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menyimpan data',
                    });
                }
            },
            error: function(xhr, status, error) {
                // Tampilkan pesan error jika ada kesalahan
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat mengirim data!',
                });
                console.error(xhr.responseText);
            }
        });
    });

    $(document).on('click', '.edit-anggaran', function() {
        const anggaranId = $(this).data('id');
        const label = $(this).data('label');
        const catatan = $(this).data('catatan');
        const url = $(this).data('data-url');

        Swal.fire({
            title: 'Edit anggaran',
            html: `
            <input id="swal-input-label" class="swal2-input" placeholder="Label" value="${label}">
            <textarea id="swal-input-catatan" class="swal2-textarea" placeholder="Catatan">${catatan}</textarea>
        `,
            focusConfirm: false,
            preConfirm: () => {
                return {
                    label_anggaran: document.getElementById('swal-input-label').value,
                    catatan_anggaran: document.getElementById('swal-input-catatan').value
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX request untuk mengupdate data
                $.ajax({
                    url: url, // URL untuk update data
                    method: 'PUT', // Metode HTTP untuk update
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}" // Token CSRF untuk Laravel
                    },
                    data: result.value, // Data yang dikirim
                    success: function(response) {
                        if (response.success) {
                            // Update tabel secara real-time
                            const row = $(`button[data-id="${anggaranId}"]`).closest('tr');
                            row.find('td:eq(1)').text(result.value.label_anggaran); // Update Label
                            row.find('td:eq(2)').text(result.value.catatan_anggaran); // Update Catatan

                            // Tampilkan alert SweetAlert jika berhasil
                            Swal.fire(
                                'Terupdate!',
                                'Data telah diperbarui.',
                                'success'
                            );
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal mengupdate data',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan saat mengupdate data!';
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage,
                        });
                        console.error(xhr.responseText);
                    }

                });
            }
        });
    });
</script>

@endsection