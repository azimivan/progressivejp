@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<h2>Edit Role</h2>

<form action="{{ route('roles.update', $role->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="name">Role Name</label>
        <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
    </div>

    <div class="form-group">
        <label for="permissions">Assign Permissions</label>
        @foreach($permissions as $permission)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                    {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                <label class="form-check-label">
                    {{ $permission->name }}
                </label>
            </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-primary">Update Role</button>
</form>
@endsection