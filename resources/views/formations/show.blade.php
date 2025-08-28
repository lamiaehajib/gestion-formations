@extends('layouts.app')

@section('content')
<div class="relative text-white overflow-hidden py-16 md:py-24 hero-section" style="background: linear-gradient(to bottom right, #D32F2F, #C2185B);">
    {{-- Subtle texture overlay for hero section --}}
    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiMwMDAwMDAiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDE4aDEyNXYxMjhIMzZ6TTM2IDM2aDEwM1YwSDM2ek00OCAzNmgyMnY0NUg0OHoiLz48L2c+PC9zdmc+'); opacity: 0.1;"></div>
    <div class="absolute inset-0 bg-black opacity-30"></div>
    
    <div class="relative container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <div class="mb-8 text-left">
                <a href="{{ route('formations.index') }}" class="inline-flex items-center text-white/90 hover:text-white transition-colors duration-300 group">
                    <i class="fa-solid fa-arrow-left fa-xl mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                    Retour aux formations
                </a>
            </div>

            <h1 class="text-4xl md:text-6xl font-extrabold mb-4 leading-tight animate-fade-in-up">
                {{ $formation->title }}
            </h1>

            <div class="mb-8 animate-fade-in-up" style="animation-delay: 0.2s">
                <span class="inline-flex items-center px-5 py-2 rounded-full bg-[#ef4444] text-white text-base font-medium shadow-lg">
                    <i class="fa-solid fa-tag fa-xl mr-2"></i>
                    {{ $formation->category->name }}
                </span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="bg-white/15 backdrop-blur-sm rounded-xl p-4 md:p-6 shadow-lg border border-white/20 transition-all duration-300 hover:scale-105 hover:bg-white/20">
                    <div class="flex items-center justify-center mb-3">
                        <i class="fa-solid fa-dollar-sign fa-2x text-green-300"></i>
                    </div>
                    <p class="text-3xl font-bold">{{ number_format($formation->price, 0) }} DH</p>
                    <p class="text-sm text-white/80">Prix</p>
                </div>

                <div class="bg-white/15 backdrop-blur-sm rounded-xl p-4 md:p-6 shadow-lg border border-white/20 transition-all duration-300 hover:scale-105 hover:bg-white/20">
                    <div class="flex items-center justify-center mb-3">
                        <i class="fa-solid fa-clock fa-2x text-blue-300"></i>
                    </div>
                    <p class="text-3xl font-bold">{{ $formation->duration_hours }} {{ $formation->duration_unit }}</p>
                    <p class="text-sm text-white/80">Durée</p>
                </div>

                <div class="bg-white/15 backdrop-blur-sm rounded-xl p-4 md:p-6 shadow-lg border border-white/20 transition-all duration-300 hover:scale-105 hover:bg-white/20">
                    <div class="flex items-center justify-center mb-3">
                        <i class="fa-solid fa-users fa-2x text-purple-300"></i>
                    </div>
                    <p class="text-3xl font-bold">{{ $availableSpots }}</p>
                    <p class="text-sm text-white/80">Places disponibles</p>
                </div>

                <div class="bg-white/15 backdrop-blur-sm rounded-xl p-4 md:p-6 shadow-lg border border-white/20 transition-all duration-300 hover:scale-105 hover:bg-white/20">
                    <div class="flex items-center justify-center mb-3">
                        <i class="fa-solid fa-star fa-2x text-yellow-300"></i>
                    </div>
                    <p class="text-3xl font-bold">{{ $averageRating ? number_format($averageRating, 1) : 'N/A' }}</p>
                    <p class="text-sm text-white/80">Note moyenne</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2 space-y-10">
            <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 transform hover:scale-[1.01] transition-all duration-300 animate-fade-in-up">
                <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center border-b pb-4 border-gray-200">
                    <i class="fa-solid fa-file-alt fa-2x mr-3 text-[#D32F2F]"></i>
                    Description
                </h2>
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                    {!! $formation->description !!}
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 transform hover:scale-[1.01] transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.2s">
                <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center border-b pb-4 border-gray-200">
                    <i class="fa-solid fa-calendar-alt fa-2x mr-3 text-[#C2185B]"></i>
                    Détails de la formation
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-red-50 rounded-xl border border-red-100">
                            <i class="fa-solid fa-calendar-day fa-lg text-[#D32F2F] mr-3 flex-shrink-0"></i>
                            <div>
                                <p class="font-semibold text-gray-900">Date de début</p>
                                <p class="text-gray-600">{{ \Carbon\Carbon::parse($formation->start_date)->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center p-4 bg-pink-50 rounded-xl border border-pink-100">
                            <i class="fa-solid fa-calendar-check fa-lg text-[#C2185B] mr-3 flex-shrink-0"></i>
                            <div>
                                <p class="font-semibold text-gray-900">Date de fin</p>
                                <p class="text-gray-600">{{ \Carbon\Carbon::parse($formation->end_date)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-green-50 rounded-xl border border-green-100">
                            <i class="fa-solid fa-users-line fa-lg text-green-600 mr-3 flex-shrink-0"></i>
                            <div>
                                <p class="font-semibold text-gray-900">Capacité</p>
                                <p class="text-gray-600">{{ $formation->capacity }} participants</p>
                            </div>
                        </div>

                       <div class="flex items-center p-4 bg-orange-50 rounded-xl border border-orange-100">
                            <i class="fa-solid fa-hourglass-half fa-lg text-orange-600 mr-3 flex-shrink-0"></i>
                            <div>
                                <p class="font-semibold text-gray-900">Durée</p>
                                <p class="text-gray-600">{{ $formation->duration_hours }} {{ $formation->duration_unit }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t pt-6 border-gray-200">
                    <div class="flex items-center p-4 bg-purple-50 rounded-xl border border-purple-100">
                        <i class="fa-solid fa-chalkboard-teacher fa-lg text-[#C2185B] mr-3 flex-shrink-0"></i>
                        <div>
                            <p class="font-semibold text-gray-900">Consultant</p>
                            <p class="text-gray-600">{{ $formation->consultant->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($formation->prerequisites && count($formation->prerequisites) > 0)
            <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 transform hover:scale-[1.01] transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.4s">
                <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center border-b pb-4 border-gray-200">
                    <i class="fa-solid fa-clipboard-list fa-2x mr-3 text-[#ef4444]"></i>
                    Prérequis
                </h2>
                <ul class="space-y-4">
                    @foreach($formation->prerequisites as $prerequisite)
                    <li class="flex items-start bg-red-50 p-3 rounded-lg border border-red-100">
                        <i class="fa-solid fa-check-circle fa-lg text-[#ef4444] mt-1 mr-3 flex-shrink-0"></i>
                        <span class="text-gray-700 font-medium">{{ $prerequisite }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if($formation->documents_required && count($formation->documents_required) > 0)
                <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 transform hover:scale-[1.01] transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.6s">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center border-b pb-4 border-gray-200">
                        <i class="fa-solid fa-file-upload fa-2x mr-3 text-green-600"></i>
                        Documents requis
                    </h2>
                    <ul class="space-y-4">
                        @foreach($formation->documents_required as $document)
                            <li class="flex items-start bg-green-50 p-3 rounded-lg border border-green-100">
                                <i class="fa-solid fa-file-alt fa-lg text-green-500 mt-1 mr-3 flex-shrink-0"></i>
                                
                                {{-- Check if the document has a 'path' to make it clickable --}}
                                @if(isset($document['path']) && $document['path'])
                                    <a href="{{ asset('storage/' . $document['path']) }}" target="_blank" class="text-green-700 font-medium hover:underline flex-grow">
                                        {{ $document['name'] ?? 'Document' }} {{-- Display the name, or 'Document' if name is missing --}}
                                        <i class="fas fa-external-link-alt ml-2 text-sm"></i> {{-- Icon to indicate it's an external link --}}
                                    </a>
                                @else
                                    {{-- If no path is available, display just the name --}}
                                    <span class="text-gray-700 font-medium">{{ $document['name'] ?? $document }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-xl p-8 md:p-10 sticky top-12 transform hover:scale-[1.01] transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.8s">
                <div class="text-center mb-8">
                    <div class="text-5xl font-extrabold text-gray-900 mb-2">{{ number_format($formation->price, 0) }} DH</div>
                    <p class="text-gray-600 text-lg">Prix de la formation</p>
                </div>

                @if($isEnrolled)
                    <div class="mb-6 p-5 bg-green-50 rounded-xl border border-green-200 text-center">
                        <div class="flex items-center justify-center">
                            <i class="fa-solid fa-circle-check fa-2x text-green-600 mr-3"></i>
                            <div>
                                <p class="font-bold text-green-800 text-xl">Inscription confirmée!</p>
                                <p class="text-base text-green-600 mt-1">Statut: <span class="font-semibold">{{ ucfirst($userInscription->status) }}</span></p>
                            </div>
                        </div>
                    </div>
                @else
                    @if($availableSpots > 0)
                        <form action="{{ route('inscriptions.store') }}" method="POST" class="mb-6">
                            @csrf
                            <input type="hidden" name="formation_id" value="{{ $formation->id }}">
                            <button type="submit" class="w-full bg-gradient-to-r from-[#D32F2F] to-[#C2185B] hover:from-[#C2185B] hover:to-[#D32F2F] text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center pulse-button">
                                <i class="fa-solid fa-user-plus fa-lg inline mr-3"></i>
                                S'inscrire maintenant
                            </button>
                        </form>
                    @else
                        <div class="mb-6 p-5 bg-red-50 rounded-xl border border-red-200 text-center">
                            <div class="flex items-center justify-center">
                                <i class="fa-solid fa-circle-xmark fa-2x text-red-600 mr-3"></i>
                                <div>
                                    <p class="font-bold text-red-800 text-xl">Complet!</p>
                                    <p class="text-base text-red-600 mt-1">Aucune place disponible pour le moment.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                @if($formation->available_payment_options && count($formation->available_payment_options) > 0)
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center text-lg">
                        <i class="fa-solid fa-credit-card fa-lg mr-3 text-[#D32F2F]"></i>
                        Options de paiement
                    </h3>
                    <div class="space-y-3">
                        @foreach($formation->available_payment_options as $option)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-base font-medium text-gray-700">{{ $option }} mois</span>
                            <span class="text-base font-bold text-[#C2185B]">{{ number_format($formation->price / $option, 0) }} DH/mois</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="space-y-4 mt-8 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between p-4 bg-pink-50 rounded-lg border border-pink-100">
                        <div class="flex items-center">
                            <i class="fa-solid fa-user-group fa-lg text-[#C2185B] mr-2"></i>
                            <span class="text-base font-medium text-gray-700">Places restantes</span>
                        </div>
                        <span class="text-base font-bold text-[#C2185B]">{{ $availableSpots }}/{{ $formation->capacity }}</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                        <div class="flex items-center">
                            <i class="fa-solid fa-star-half-stroke fa-lg text-yellow-600 mr-2"></i>
                            <span class="text-base font-medium text-gray-700">Note moyenne</span>
                        </div>
                        <span class="text-base font-bold text-yellow-600">{{ $averageRating ? number_format($averageRating, 1) . '/5' : 'N/A' }}</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-100">
                        <div class="flex items-center">
                            <i class="fa-solid fa-receipt fa-lg text-[#ef4444] mr-2"></i>
                            <span class="text-base font-medium text-gray-700">Inscriptions</span>
                        </div>
                        <span class="text-base font-bold text-[#ef4444]">{{ $formation->inscriptions->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fade-in-up 0.6s ease-out forwards;
        opacity: 0; /* Ensures element is hidden before animation starts */
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Pulse animation for buttons */
    .pulse-button {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.02); /* Slightly less intense pulse */
        }
    }

    /* Parallax effect for hero section */
    .hero-section {
        background-attachment: fixed; /* For true parallax effect with background image */
    }

    /* Scrollbar styling */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(45deg, #D32F2F, #C2185B); /* Red to Magenta */
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(45deg, #C2185B, #D32F2F);
    }
    i.fa-solid.fa-file-alt.fa-2x.mr-3.text-\[\#D32F2F\] {
    font-size: 27px !important;
}
i.fa-solid.fa-calendar-alt.fa-2x.mr-3.text-\[\#C2185B\] {
    font-size: 27px !important;
}
.bg-black {
    --bs-bg-opacity: 1;
    background-color: rgb(225 46 46) !important;
}
i.fa-solid.fa-clipboard-list.fa-2x.mr-3.text-\[\#ef4444\] {
    font-size: 27px !important;
}
</style>

@endsection

@section('scripts')
<script>
    // Smooth scrolling for anchor links (if any)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -80px 0px' // Adjust to trigger animation earlier/later
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
                observer.unobserve(entry.target); // Stop observing once animated
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-fade-in-up').forEach(el => {
        observer.observe(el);
    });

    // Share functionality
    document.querySelectorAll('[data-share]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const platform = this.dataset.share;
            const url = window.location.href;
            const title = document.title;
            
            let shareUrl = '';
            
            switch(platform) {
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
                    break;
                case 'linkedin':
                    shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
                    break;
            }
            
            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        });
    });

    // Parallax effect for hero section (enhanced)
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            heroSection.style.backgroundPositionY = `${-scrolled * 0.2}px`; 
        });
    }
</script>
@endsection
