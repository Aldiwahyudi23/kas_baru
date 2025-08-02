<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Rekening Bank</h3>
       
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="bankAccountsTable" class="table table-bordered table-striped datatable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pemilik</th>
                    <th>Bank & No. Rekening</th>
                    <th>Nama Pemilik</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bankAccount as $index => $account)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $account->warga->name ?? '-' }}
                       
                    </td>
                    <td>
                        <strong>{{ $account->bank_name }}</strong>
                        <br>{{ $account->account_number }}
                    </td>
                    <td>{{ $account->account_holder_name }}</td>
                    <td>
                        <form class="statusForm" method="POST" action="{{ route('bank-accounts.toggle-status', $account->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="button" class="btn btn-sm {{ $account->is_active ? 'btn-success' : 'btn-danger' }} toggle-status"
                                data-id="{{ $account->id }}"
                                data-active="{{ $account->is_active ? 1 : 0 }}">
                                {{ $account->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="project-actions text-right">
                        <a class="btn btn-primary btn-sm" href="{{ route('bank-accounts.show', $account->id) }}">
                            <i class="fas fa-folder"></i> View
                        </a>
                        <a class="btn btn-info btn-sm" href="{{ route('bank-accounts.edit', $account->id) }}">
                            <i class="fas fa-pencil-alt"></i> Edit
                        </a>
                        <form action="{{ route('bank-accounts.destroy', $account->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus rekening ini?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        $('#bankAccountsTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            }
        });

        // Toggle status aktif
        $('.toggle-status').click(function() {
            let button = $(this);
            let form = button.closest('form');
            let isActive = button.data('active');
            let newStatus = isActive ? 0 : 1;
            
            $.ajax({
                url: form.attr('action'),
                type: 'PATCH',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    is_active: newStatus
                },
                success: function(response) {
                    if(response.success) {
                        button.data('active', newStatus);
                        if(newStatus) {
                            button.removeClass('btn-danger').addClass('btn-success').text('Aktif');
                        } else {
                            button.removeClass('btn-success').addClass('btn-danger').text('Nonaktif');
                        }
                        Toast.fire({
                            icon: 'success',
                            title: 'Status berhasil diubah'
                        });
                    }
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan'
                    });
                }
            });
        });
    });
</script>
@endsection