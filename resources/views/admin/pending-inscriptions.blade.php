@extends('layouts.app') {{-- يفترض أن هذا هو الـ layout الرئيسي للوحة تحكم المسؤول --}}

@section('title', __('Validation des Inscriptions') . ' - Portail Étudiant UITS')

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

                    <h3 class="h5 mb-16">{{ __('Inscriptions en attente de validation:') }}</h3>

                    @if ($pendingInscriptions->isEmpty())
                        <p>{{ __('Aucune inscription en attente de validation pour le moment.') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('ID Inscription') }}</th>
                                        <th>{{ __('Étudiant') }}</th>
                                        <th>{{ __('Formation') }}</th>
                                        <th>{{ __('Plan de Paiement') }}</th>
                                        <th>{{ __('Montant Total') }}</th>
                                        <th>{{ __('Montant Initial Payé') }}</th>
                                        <th>{{ __('Preuve de Paiement') }}</th>
                                        <th>{{ __('Date Inscription') }}</th>
                                        <th>{{ __('Notes') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingInscriptions as $inscription)
                                        <tr>
                                            <td>{{ $inscription->id }}</td>
                                            <td>{{ $inscription->user->name ?? 'N/A' }}<br><small>{{ $inscription->user->email ?? '' }}</small></td>
                                            <td>{{ $inscription->formation->title ?? 'N/A' }}</td>
                                            <td>{{ $inscription->payment_plan }}</td>
                                            <td>{{ number_format($inscription->total_amount, 2) }} DH</td>
                                            <td>{{ number_format($inscription->paid_amount, 2) }} DH</td>
                                            <td>
                                                @php
                                                    $documents = json_decode($inscription->documents, true);
                                                    $proofDoc = collect($documents)->firstWhere('type', 'proof_of_payment');
                                                @endphp
                                                @if ($proofDoc && isset($proofDoc['file_path']))
                                                    <a href="{{ Storage::url($proofDoc['file_path']) }}" target="_blank" class="btn btn-sm btn-info">{{ __('Voir Preuve') }}</a>
                                                @else
                                                    {{ __('N/A') }}
                                                @endif
                                            </td>
                                            <td>{{ $inscription->inscription_date->format('d/m/Y') }}</td>
                                            <td>{{ Str::limit($inscription->notes, 50) }}</td>
                                            <td>
                                                <form action="{{ route('admin.validate_inscription', $inscription->id) }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir valider cette inscription et la marque comme payée initialement ?') }}');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">{{ __('Valider l\'Inscription') }}</button>
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