@extends('errors::minimal')

@section('title', 'Trop de Requêtes')
@section('code', '429')
@section('message', 'Vous avez envoyé trop de requêtes en peu de temps. Veuillez réessayer plus tard.')
@section('icon')
    <i class="fas fa-traffic-light icon"></i>
@endsection