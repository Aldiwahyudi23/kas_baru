@extends('user.layout.app')

@section('content')
<div class="container">
    <h1>Member Types</h1>
    <a href="{{ route('member-types.create') }}" class="btn btn-primary">Tambah Data Type Member</a>
    <table class="table mt-4">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($types as $type)
            <tr>
                <td>{{ $type->id }}</td>
                <td>{{ $type->name }}</td>
                <td>{{ $type->description }}</td>
                <td>
                    <a href="{{ route('member-types.edit', $type->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('member-types.destroy', $type->id) }}" method="POST"
                        style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection