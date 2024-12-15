            <div class="card card-secondary">
                <div class="card-header d-flex justify-content-between align-items-center">


                    <div class="form-group align-items-right">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitchWaNotification"
                                wire:click="toggle('waNotification')" {{ $waNotification ? 'checked' : '' }}>
                            <label class="custom-control-label" for="customSwitchWaNotification">WA</label>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitchEmailNotification"
                                wire:click="toggle('emailNotification')" {{ $emailNotification ? 'checked' : '' }}>
                            <label class="custom-control-label" for="customSwitchEmailNotification">Email</label>
                        </div>
                    </div>
                    <h3 class="card-title mb-0">Access Notifikasi</h3>
                </div>

                <!-- /.card-header -->
                <div class="card-body">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitchPengurus"
                                wire:click="toggle('pengurus')" {{ $pengurus ? 'checked' : '' }}>
                            <label class="custom-control-label" for="customSwitchPengurus">Pengurus</label>
                        </div>
                        <small class="text-muted">
                            Centang untuk mengaktifkan fitur notifikasi untuk Semua Pengurus.
                        </small>
                    </div>
                    <hr>
                    @if ($pengurus)
                    <small class="text-muted">
                        Di bawah adalah access untuk pengurus yang akan menerima notifikasi
                    </small>
                    <div class="row">
                        <!-- Foreach Yang menampilkan data Access Notification berdasarkan notification_id yang saat ini -->
                        @foreach ($roles as $role)
                        <div class="col-4 col-sm-4">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox"
                                        id="customCheckbox{{ $role->id }}" wire:click="toggleRole({{ $role->id }})"
                                        {{ $roleStatus[$role->id] ? 'checked' : '' }}>
                                    <label for="customCheckbox{{ $role->id }}" class="custom-control-label">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <hr>
                    @endif
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitchAnggota"
                                wire:click="toggle('anggota')" {{ $anggota ? 'checked' : '' }}>
                            <label class="custom-control-label" for="customSwitchAnggota">Anggota</label>
                        </div>
                        <small class="text-muted">
                            Centang untuk mengaktifkan fitur notifikasi untuk anggota yang mengajukan.
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitchProgram"
                                wire:click="toggle('program')" {{ $program ? 'checked' : '' }}>
                            <label class="custom-control-label" for="customSwitchProgram">Program</label>
                        </div>
                        <small class="text-muted">
                            Untuk Notifikasi yang di kirim ke semua anggota KAS
                        </small>
                    </div>

                </div>
                <!-- /.card-body -->
            </div>