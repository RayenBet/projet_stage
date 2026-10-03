@extends('layouts.student')

@section('title', $certificate->name . ' - Certificat')
@section('page_title', $certificate->name)
@section('page_subtitle', $certificate->isExternal() ? 'Certification professionnelle' : 'Certificat d\'assiduité')
@section('active_certificates', 'active')

@section('content')

<style>
    .detail-hero {
        border-radius: 24px; padding: 40px; position: relative; overflow: hidden; color: white;
        background: linear-gradient(135deg, {{ $badge['color'] }} 0%, #0F172A 120%);
    }
    .detail-hero::before {
        content: ''; position: absolute; top: -70px; right: -70px; width: 260px; height: 260px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%); border-radius: 50%;
    }
    .detail-icon {
        width: 88px; height: 88px; border-radius: 22px; background: rgba(255,255,255,0.15); backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,0.25); display: flex; align-items: center; justify-content: center; font-size: 36px; flex-shrink: 0;
    }
    .info-card { background: white; border-radius: 18px; border: 1px solid #F1F5F9; padding: 22px; }
    .step-item { display: flex; gap: 14px; padding: 14px 0; border-bottom: 1px solid #F1F5F9; }
    .step-item:last-child { border-bottom: none; }
    .step-num {
        width: 30px; height: 30px; border-radius: 10px; background: #EEF2FF; color: #4F46E5; font-weight: 700; font-size: 13px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .nav-cert-link {
        display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 14px; border: 1px solid #F1F5F9;
        transition: all 0.2s ease; flex: 1;
    }
    .nav-cert-link:hover { border-color: #C7D2FE; background: #F8FAFC; }
    .skill-tag {
        display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; padding: 8px 14px;
        border-radius: 12px; background: #F8FAFC; border: 1px solid #F1F5F9; color: #334155;
    }
    .skill-tag i { color: {{ $badge['color'] }}; font-size: 12px; }
    .external-cta {
        display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px; border-radius: 14px; font-weight: 700; font-size: 14px;
        background: {{ $badge['color'] }}; color: white; transition: opacity 0.2s ease;
    }
    .external-cta:hover { opacity: 0.9; }
</style>

<a href="{{ route('student.certificates') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 mb-5 font-medium">
    <i class="fas fa-arrow-left"></i> Retour à tous les certificats
</a>

<!-- HERO DÉTAIL -->
<div class="detail-hero mb-8">
    <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
        <div class="detail-icon"><i class="fas {{ $badge['icon'] }}"></i></div>
        <div class="flex-1 text-center md:text-left">
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-2">
                <span class="text-xs font-bold uppercase tracking-wide px-3 py-1 rounded-full" style="background: rgba(255,255,255,0.2);">
                    {{ $certificate->isExternal() ? ($certificate->category ?? 'Certification') : 'Palier ' . $badge['label'] }}
                </span>
                @if($certificate->level)
                    <span class="text-xs font-bold uppercase tracking-wide px-3 py-1 rounded-full" style="background: rgba(255,255,255,0.2);">
                        Niveau {{ $certificate->level }}
                    </span>
                @endif
                @if($unlocked)
                    <span class="text-xs font-bold uppercase tracking-wide px-3 py-1 rounded-full bg-green-400/90 text-green-950">
                        <i class="fas fa-check"></i> Obtenu
                    </span>
                @else
                    <span class="text-xs font-bold uppercase tracking-wide px-3 py-1 rounded-full bg-white/15">
                        <i class="fas fa-hourglass-half"></i> Non obtenu
                    </span>
                @endif
            </div>
            <h2 class="text-3xl font-extrabold mb-1">{{ $certificate->name }}</h2>
            @if($certificate->provider)
                <p class="text-white/70 font-semibold text-sm mb-2">
                    <i class="fas fa-building mr-1"></i> Délivré par {{ $certificate->provider }}
                </p>
            @endif
            <p class="text-white/80 max-w-xl">{{ $certificate->description }}</p>
        </div>

        <div class="text-center bg-white/10 rounded-2xl p-5 backdrop-blur-sm border border-white/15 min-w-[140px]">
            <p class="text-3xl font-extrabold">{{ $certificate->min_score }}</p>
            <p class="text-xs text-white/70 uppercase tracking-wide">Points requis</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-6">

        <!-- PROGRESSION + RÉCLAMATION (commune à TOUS les certificats) -->
        <div class="info-card">
            <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-line" style="color: {{ $badge['color'] }};"></i>
                {{ $unlocked ? 'Certificat obtenu' : 'Votre progression' }}
            </h4>

            @if($unlocked)
                <div class="flex items-center gap-4 bg-green-50 rounded-xl p-4">
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="font-bold text-green-800">Vous possédez ce certificat</p>
                        <p class="text-sm text-green-700">
                            @if($unlockedAt) Réclamé le {{ \Carbon\Carbon::parse($unlockedAt)->format('d/m/Y') }} • {{ $certificate->min_score }} points dépensés
                            @else Réclamé avec succès. @endif
                        </p>
                    </div>
                </div>

            @elseif($canClaim)
                <div class="rounded-xl p-5 border-2 border-dashed" style="background: {{ $badge['bg'] }}; border-color: {{ $badge['color'] }}66;">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg" style="background: {{ $badge['color'] }}; color: white;">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Vous avez assez de points !</p>
                            <p class="text-sm text-gray-500">Vous pouvez réclamer ce certificat dès maintenant.</p>
                        </div>
                    </div>

                    <form id="claimForm" action="{{ route('student.certificates.claim', $certificate->id) }}" method="POST">
                        @csrf
                        <button type="button" onclick="openClaimModal()" class="w-full py-3 rounded-xl font-bold text-white transition hover:opacity-90"
                                style="background: {{ $badge['color'] }};">
                            <i class="fas fa-hand-sparkles mr-1"></i>
                            Réclamer ce certificat <span class="opacity-80">(-{{ $certificate->min_score }} pts)</span>
                        </button>
                    </form>
                    <p class="text-xs text-gray-400 mt-2 text-center">
                        Votre score actuel : {{ $student->score }} pts • Après réclamation : {{ $student->score - $certificate->min_score }} pts
                    </p>
                </div>

                <!-- Modale de confirmation stylée (remplace le confirm() natif du navigateur) -->
                <div id="claimModalOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9998] hidden items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl" style="animation: claim-modal-in 0.25s cubic-bezier(0.16,1,0.3,1);">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4" style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }};">
                            <i class="fas fa-gift"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Confirmer la réclamation</h3>
                        <p class="text-sm text-gray-500 text-center mb-5">
                            Réclamer <span class="font-semibold text-gray-800">{{ $certificate->name }}</span> coûtera
                            <span class="font-semibold text-gray-800">{{ $certificate->min_score }} points</span>,
                            déduits de votre score actuel ({{ $student->score }} pts).
                        </p>
                        <div class="flex gap-3">
                            <button type="button" onclick="closeClaimModal()" class="flex-1 py-2.5 rounded-xl bg-gray-100 text-gray-600 font-semibold text-sm hover:bg-gray-200 transition">
                                Annuler
                            </button>
                            <button type="button" onclick="document.getElementById('claimForm').submit()" class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm transition hover:opacity-90" style="background: {{ $badge['color'] }};">
                                Confirmer
                            </button>
                        </div>
                    </div>
                </div>

                <style>
                    @keyframes claim-modal-in { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
                </style>

                <script>
                    function openClaimModal() {
                        const overlay = document.getElementById('claimModalOverlay');
                        overlay.classList.remove('hidden');
                        overlay.classList.add('flex');
                    }
                    function closeClaimModal() {
                        const overlay = document.getElementById('claimModalOverlay');
                        overlay.classList.add('hidden');
                        overlay.classList.remove('flex');
                    }
                </script>

            @else
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="text-gray-500">{{ $student->score ?? 0 }} / {{ $certificate->min_score }} pts</span>
                    <span class="font-bold" style="color: {{ $badge['color'] }};">{{ round($progress) }}%</span>
                </div>
                <div class="progress-bar-cert" style="height: 10px;">
                    <div class="fill" style="width: {{ $progress }}%; background: {{ $badge['color'] }};"></div>
                </div>
                <p class="text-sm text-gray-500 mt-3">
                    Il vous manque <span class="font-bold text-gray-800">{{ $pointsRemaining }} points</span> pour pouvoir réclamer ce certificat.
                </p>
            @endif
        </div>

        <!-- À QUOI SERT CE CERTIFICAT (commun à tous les certificats) -->
        <div class="info-card">
            <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                <i class="fas fa-bullseye" style="color: {{ $badge['color'] }};"></i> À quoi sert ce certificat ?
            </h4>
            <p class="text-sm text-gray-600 leading-relaxed">
                {{ $certificate->utility ?? "Ce certificat atteste d'un niveau de compétence ou de régularité reconnu, validant vos efforts et votre sérieux dans ce domaine." }}
            </p>
        </div>

        <!-- AVANTAGES CARRIÈRE (commun à tous les certificats) -->
        <div class="info-card" style="background: linear-gradient(135deg, {{ $badge['bg'] }} 0%, white 60%);">
            <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                <i class="fas fa-briefcase" style="color: {{ $badge['color'] }};"></i> Avantages pour votre carrière
            </h4>
            <p class="text-sm text-gray-600 leading-relaxed">
                {{ $certificate->career_benefits ?? "Ce type de certification renforce votre profil professionnel et constitue un signal positif auprès des recruteurs, en attestant de votre engagement et de votre montée en compétences." }}
            </p>
        </div>

        @if($certificate->isExternal())
            <!-- CONTENU RICHE (certifications externes) -->
            <div class="info-card">
                <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-graduation-cap" style="color: {{ $badge['color'] }};"></i> À propos de cette certification
                </h4>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $certificate->description }}</p>
            </div>

            @if($certificate->skills_list)
                <div class="info-card">
                    <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-bullseye" style="color: {{ $badge['color'] }};"></i> Compétences évaluées
                    </h4>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($certificate->skills_list as $skill)
                            <span class="skill-tag"><i class="fas fa-check-circle"></i> {{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        @if($certificate->external_url)
            <a href="{{ $certificate->external_url }}" target="_blank" rel="noopener" class="external-cta">
                <i class="fas fa-external-link-alt"></i> Voir les informations officielles
            </a>
        @endif

        @if($previousCertificate || $nextCertificateInList)
        <div class="flex flex-col sm:flex-row gap-4">
            @if($previousCertificate)
                <a href="{{ route('student.certificates.show', $previousCertificate->id) }}" class="nav-cert-link">
                    <i class="fas fa-chevron-left text-gray-400"></i>
                    <div><p class="text-xs text-gray-400">Précédent</p><p class="font-semibold text-gray-900 text-sm">{{ $previousCertificate->name }}</p></div>
                </a>
            @endif
            @if($nextCertificateInList)
                <a href="{{ route('student.certificates.show', $nextCertificateInList->id) }}" class="nav-cert-link justify-end text-right">
                    <div><p class="text-xs text-gray-400">Suivant</p><p class="font-semibold text-gray-900 text-sm">{{ $nextCertificateInList->name }}</p></div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
            @endif
        </div>
        @endif
    </div>

    <!-- Colonne latérale -->
    <div class="space-y-6">
        <div class="info-card text-center">
            <div class="w-16 h-16 rounded-2xl mx-auto flex items-center justify-center text-2xl mb-3"
                 style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }};">
                <i class="fas {{ $badge['icon'] }}"></i>
            </div>
            <p class="font-bold text-gray-900">{{ $certificate->isExternal() ? ($certificate->category ?? 'Certification') : 'Palier ' . $badge['label'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Seuil fixé à {{ $certificate->min_score }} points</p>
        </div>

        <div class="info-card">
            <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2 text-sm">
                <i class="fas fa-info-circle text-indigo-600"></i> Informations
            </h4>
            @if($certificate->provider)
            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                <span class="text-sm text-gray-500">Organisme</span>
                <span class="font-bold text-gray-900 text-sm text-right">{{ $certificate->provider }}</span>
            </div>
            @endif
            @if($certificate->level)
            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                <span class="text-sm text-gray-500">Niveau</span>
                <span class="font-bold text-gray-900 text-sm">{{ $certificate->level }}</span>
            </div>
            @endif
            @if($certificate->duration)
            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                <span class="text-sm text-gray-500">Préparation</span>
                <span class="font-bold text-gray-900 text-sm">{{ $certificate->duration }}</span>
            </div>
            @endif
            <div class="flex items-center justify-between py-2">
                <span class="text-sm text-gray-500">Étudiants certifiés</span>
                <span class="font-bold text-gray-900">{{ $holdersCount }}</span>
            </div>
        </div>
    </div>
</div>

@endsection