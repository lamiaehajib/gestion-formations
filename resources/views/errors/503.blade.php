@extends('errors::minimal')

@section('title', 'Service Indisponible')
@section('code', '503')
@section('message', 'Le service est temporairement indisponible pour cause de maintenance. Veuillez réessayer plus tard.')
@section('icon')
    <i class="fas fa-tools icon"></i>
@endsection