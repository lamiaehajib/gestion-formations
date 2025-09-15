@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Formations & Modules</h2>
        <a href="{{ route('modules.create') }}" class="btn btn-primary">Create New Module</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Formation Title</th>
                        <th>Number of Modules</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formations as $formation)
                    <tr>
                        <td>{{ $formation->title }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $formation->modules_count }} Modules</span>
                        </td>
                        <td>
                            <a href="{{ route('modules.show', ['formation' => $formation->id]) }}" class="btn btn-info btn-sm">View Modules</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection