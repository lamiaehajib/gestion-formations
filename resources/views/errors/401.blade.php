@extends('errors::minimal')

@section('title', 'Accès Non Autorisé')
@section('code', '401')
@section('message', 'Vous n\'êtes pas autorisé à accéder à cette page.')
@section('icon')
    <i class="fas fa-user-lock icon"></i>
@endsection