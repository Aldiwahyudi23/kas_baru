@extends('user.layout.app')

@section('content')
<div class="container">
    <h1>Members</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Member Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($members as $member)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $member->user->name }}</td>
                <td>{{ $member->memberType->name }}</td>
                <td>
                    <button class="btn btn-sm toggle-active-btn {{ $member->is_active ? 'btn-success' : 'btn-danger' }}"
                        data-id="{{ $member->id }}">
                        {{ $member->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </td>
                <td>
                    <a href="{{ route('members.edit', $member->id) }}" class="btn btn-primary btn-sm">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on('click', '.toggle-active-btn', function() {
    console.log('Button clicked'); // Debugging
    let button = $(this);
    let memberId = button.data('id');
    console.log('Member ID:', memberId); // Debugging

    $.ajax({
        url: `/members/${memberId}/toggle-active`,
        type: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            console.log('Response:', response); // Debugging
            if (response.success) {
                if (response.is_active) {
                    button.removeClass('btn-danger').addClass('btn-success').text('Active');
                } else {
                    button.removeClass('btn-success').addClass('btn-danger').text('Inactive');
                }
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText); // Debugging
            alert('Failed to toggle status.');
        }
    });
});
</script>
@endsection