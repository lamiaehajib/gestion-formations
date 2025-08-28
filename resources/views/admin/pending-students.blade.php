@extends('layouts.app') {{-- يفترض أن هذا هو الـ layout الرئيسي للوحة تحكم المسؤول --}}

@section('title', __('Validation des Étudiants') . ' - Portail Étudiant UITS')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- رسائل النجاح أو الخطأ --}}
                    @if (session('success'))
                        <div class="alert alert-success mb-24" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mb-24" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <h3 class="h5 mb-16">{{ __('Étudiants en attente de validation:') }}</h3>

                    @if ($pendingStudents->isEmpty())
                        <p>{{ __('Aucun étudiant en attente de validation pour le moment.') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Nom Complet') }}</th>
                                        <th>{{ __('Adresse E-mail') }}</th>
                                        <th>{{ __('Numéro CIN') }}</th>
                                        <th>{{ __('Téléphone') }}</th>
                                        <th>{{ __('Statut') }}</th>
                                        <th>{{ __('Date d\'inscription') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingStudents as $student)
                                        <tr>
                                            <td>{{ $student->id }}</td>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->email }}</td>
                                            <td>{{ $student->cin ?? 'N/A' }}</td>
                                            <td>{{ $student->phone ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-warning">{{ $student->status }}</span>
                                            </td>
                                            <td>{{ $student->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <form action="{{ route('admin.validate_student', $student->id) }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir valider le compte de cet étudiant ?') }}');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">{{ __('Valider le Compte') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection