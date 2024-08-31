@extends('layouts.app')

@section('title', 'Edit Permission')

@section('content')
<h2>Edit Permission</h2>

<form action="{{ route('permissions.update', $permission->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="name">Permission Name</label>
        <input type="text" name="name" class="form-control" value="{{ $permission->name }}" required>
    </div>

    <button type="submit" class="btn btn-primary">Update Permission</button>
</form>
@endsection