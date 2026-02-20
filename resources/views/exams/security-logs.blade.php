@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Logs de sécurité - {{ $attempt->exam->title }}</h3>
    <p>Étudiant: {{ $attempt->user->name }}</p>
    
    <div class="row mb-4">
        @foreach($suspiciousActivities as $activity => $count)
            @if($count > 0)
            <div class="col-md-4 mb-2">
                <div class="alert alert-warning">
                    <strong>{{ str_replace('_', ' ', $activity) }}:</strong> {{ $count }}
                </div>
            </div>
            @endif
        @endforeach
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Activity</th>
                <th>Tab Switches</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ Carbon\Carbon::parse($log->activity_timestamp)->format('d/m/Y H:i:s') }}</td>
                <td>{{ $log->activity_type }}</td>
                <td>{{ $log->tab_switch_count }}</td>
                <td>{{ $log->ip_address }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection