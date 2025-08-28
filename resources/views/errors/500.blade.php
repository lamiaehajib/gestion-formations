@extends('errors::minimal')

@section('title', 'Erreur Serveur')
@section('code', '500')
@section('message', 'Désolé, une erreur interne est survenue sur notre serveur.')
@section('icon')
    <i class="fas fa-server icon"></i>
@endsection