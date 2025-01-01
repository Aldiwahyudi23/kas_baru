@extends('user.layout.app')

@section('content')
<div class="container">
    <h1>Edit Member Access</h1>
    <form action="{{ route('members.update', $member->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="user_id" class="form-label">User</label>
            <select name="user_id" class="form-control" required>
                @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ $member->user_id == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="member_type_id" class="form-label">Member Type</label>
            <select name="member_type_id" class="form-control" required>
                @foreach ($memberTypes as $type)
                <option value="{{ $type->id }}" {{ $member->member_type_id == $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="is_active" class="form-label">Is Active</label>
            <select name="is_active" class="form-control" required>
                <option value="1" {{ $member->is_active ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !$member->is_active ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection