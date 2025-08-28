@extends('errors::minimal')

@section('title', 'Accès Interdit')
@section('code', '403')
@section('message', 'Désolé, vous n\'êtes pas autorisé à accéder à cette ressource.')
@section('icon')
    <i class="fas fa-ban icon"></i>
@endsection