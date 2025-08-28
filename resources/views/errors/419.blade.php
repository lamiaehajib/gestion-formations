@extends('errors::minimal')

@section('title', 'Page Expirée')
@section('code', '419')
@section('message', 'La page a expiré en raison d\'une inactivité prolongée. Veuillez actualiser et réessayer.')
@section('icon')
    <i class="fas fa-history icon"></i>
@endsection