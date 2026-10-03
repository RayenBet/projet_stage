@extends('layouts.student')

@section('title', 'Mes certificats - Étudiant')
@section('page_title', '🏅 Mes certificats')
@section('page_subtitle', 'Certificats d\'assiduité et certifications professionnelles')
@section('active_certificates', 'active')

@section('content')

<style>
    .cert-hero {
        background: linear-gradient(135deg, #0F172A 0%, #312E81 50%, #4F46E5 100%);
        border-radius: 24px; padding: 40px; position: relative; overflow: hidden; color: white; margin-bottom: 32px;
    }
    .cert-hero::before {
        content: ''; position: absolute; top: -60px; right: -60px; width: 260px; height: 260px;
        background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%); border-radius: 50%;
    }
    .hero-ring { width: 130px; height: 130px; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; flex-shrink: 0; }
    .hero-ring svg { position: absolute; top: 0; left: 0; transform: rotate(-90deg); }
    .hero-ring-inner { width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.08); backdrop-filter: blur(4px); display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 1; }

    .section-divider { display: flex; align-items: center; gap: 14px; margin: 40px 0 22px; }
    .section-divider .line { flex: 1; height: 1px; background: #E2E8F0; }
    .section-divider h4 { font-size: 15px; font-weight: 800; color: #0F172A; white-space: nowrap; display: flex; align-items: center; gap: 8px; }

    .cert-card-pro {
        background: white; border-radius: 20px; border: 1px solid #F1F5F9; overflow: hidden; cursor: pointer;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); position: relative; display: flex; flex-direction: column;
    }
    .cert-card-pro:hover { transform: translateY(-6px); box-shadow: 0 20px 40px -12px rgba(15,23,42,0.15); border-color: transparent; }
    .cert-card-pro.is-locked { opacity: 0.9; }
    .cert-card-banner { height: 90px; position: relative; display: flex; align-items: center; justify-content: center; }
    .cert-card-banner .banner-icon {
        width: 56px; height: 56px; border-radius: 16px; background: rgba(255,255,255,0.2); backdrop-filter: blur(6px);
        display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; border: 1px solid rgba(255,255,255,0.3);
    }
    .cert-tier-tag {
        position: absolute; top: 12px; left: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
        padding: 4px 10px; border-radius: 999px; background: rgba(255,255,255,0.25); color: white; backdrop-filter: blur(4px);
    }
    .cert-status-tag {
        position: absolute; top: 12px; right: 12px; width: 30px; height: 30px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-size: 14px; background: white;
    }
    .cert-card-body { padding: 18px 20px 20px; flex: 1; display: flex; flex-direction: column; }
    .cert-card-pro .view-link { margin-top: 14px; font-size: 13px; font-weight: 700; color: #4F46E5; display: flex; align-items: center; gap: 6px; }
    .skill-chip { font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 999px; background: #F1F5F9; color: #475569; }
</style>

@php
    $scorePercent = $nextCertificate ? min(100, ($student->score / max(1, $nextCertificate->min_score)) * 100) : 100;
    $circumference = 2 * 3.14159 * 54;

    // Fonction locale : calcule statut + progression réelle (basée sur le score) pour n'importe quel certificat
    function certProgress($certificate, $unlockedIds, $student) {
        $unlocked = $unlockedIds->contains($certificate->id);
        $progress = $unlocked ? 100 : min(100, (($student->score ?? 0) / max(1, $certificate->min_score)) * 100);
        $canClaim = !$unlocked && $progress >= 100;
        return compact('unlocked', 'progress', 'canClaim');
    }
@endphp

<!-- HERO -->
<div class="cert-hero">
    <div class="relative z-10 flex flex-col lg:flex-row items-center gap-8">
        <div class="hero-ring">
            <svg width="130" height="130">
                <circle cx="65" cy="65" r="54" stroke="rgba(255,255,255,0.15)" stroke-width="10" fill="none" />
                <circle cx="65" cy="65" r="54" stroke="#818CF8" stroke-width="10" fill="none"
                    stroke-dasharray="{{ $circumference }}"
                    stroke-dashoffset="{{ $circumference - ($circumference * $scorePercent / 100) }}"
                    stroke-linecap="round" />
            </svg>
            <div class="hero-ring-inner">
                <span class="text-2xl font-extrabold text-white">{{ $unlockedCount }}</span>
                <span class="text-[11px] text-indigo-200">/ {{ $totalCertificates }}</span>
            </div>
        </div>

        <div class="flex-1 text-center lg:text-left">
            <p class="text-indigo-300 text-sm font-semibold uppercase tracking-wide mb-1">Espace certifications</p>
            <h3 class="text-3xl font-extrabold mb-2">Votre vitrine de récompenses</h3>
            <p class="text-indigo-200 max-w-xl">
                Un seul et même score d'assiduité fait progresser aussi bien vos certificats internes
                que vos certifications professionnelles reconnues à l'international.
            </p>

            <div class="flex flex-wrap justify-center lg:justify-start gap-6 mt-5">
                <div>
                    <p class="text-2xl font-extrabold">{{ $student->score ?? 0 }}</p>
                    <p class="text-xs text-indigo-300">Points cumulés</p>
                </div>
                <div class="w-px bg-white/15"></div>
                <div>
                    <p class="text-2xl font-extrabold">{{ $unlockedCount }}</p>
                    <p class="text-xs text-indigo-300">Certificats obtenus</p>
                </div>
                <div class="w-px bg-white/15"></div>
                <div>
                    @if($nextCertificate)
                        <p class="text-2xl font-extrabold">{{ $nextCertificate->min_score - ($student->score ?? 0) }}</p>
                        <p class="text-xs text-indigo-300">Points avant "{{ $nextCertificate->name }}"</p>
                    @else
                        <p class="text-2xl font-extrabold">🎉</p>
                        <p class="text-xs text-indigo-300">Tous les paliers atteints</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECTION CERTIFICATIONS EXTERNES -->
<div class="section-divider">
    <h4><i class="fas fa-globe text-sky-600"></i> Certifications professionnelles</h4>
    <div class="line"></div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($externalCertificates as $certificate)
        @php $r = certProgress($certificate, $unlockedIds, $student); extract($r); $badge = $certificate->badge; @endphp
        <div class="cert-card-pro {{ $unlocked ? '' : 'is-locked' }}" onclick="window.location='{{ route('student.certificates.show', $certificate->id) }}'" style="cursor: pointer;">
            <div class="cert-card-banner" style="background: linear-gradient(135deg, {{ $badge['color'] }} 0%, {{ $badge['color'] }}CC 100%);">
                <span class="cert-tier-tag">{{ $badge['label'] }}</span>
                <span class="cert-status-tag" style="color: {{ $unlocked ? '#16A34A' : ($canClaim ? '#D97706' : '#94A3B8') }};">
                    <i class="fas {{ $unlocked ? 'fa-check' : ($canClaim ? 'fa-gift' : 'fa-external-link-alt') }}"></i>
                </span>
                <div class="banner-icon"><i class="fas {{ $badge['icon'] }}"></i></div>
            </div>
            <div class="cert-card-body">
                <div class="flex items-center gap-2 mb-1">
                    <p class="font-bold text-gray-900 text-base">{{ $certificate->name }}</p>
                    @if($certificate->level)
                        <span class="skill-chip">{{ $certificate->level }}</span>
                    @endif
                </div>
                <p class="text-xs text-gray-400 font-semibold mb-2">{{ $certificate->provider }}</p>
                <p class="text-sm text-gray-500 line-clamp-2">{{ $certificate->description }}</p>

                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-gray-500">Progression</span>
                        <span class="font-bold" style="color: {{ $unlocked ? '#16A34A' : $badge['color'] }};">{{ round($progress) }}%</span>
                    </div>
                    <div class="progress-bar-cert">
                        <div class="fill" style="width: {{ $progress }}%; background: {{ $unlocked ? '#22C55E' : $badge['color'] }};"></div>
                    </div>
                    <p class="text-xs mt-1.5 {{ $canClaim ? 'text-amber-600 font-bold' : 'text-gray-400' }}">
                        @if($unlocked) Objectif atteint • {{ $certificate->min_score }} pts requis
                        @elseif($canClaim) 🎁 Prêt à réclamer !
                        @else {{ $student->score ?? 0 }} / {{ $certificate->min_score }} pts @endif
                    </p>
                </div>

                @if($certificate->skills_list ?? false)
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        @foreach(array_slice($certificate->skills_list, 0, 2) as $skill)
                            <span class="skill-chip">{{ $skill }}</span>
                        @endforeach
                        @if(count($certificate->skills_list) > 2)
                            <span class="skill-chip">+{{ count($certificate->skills_list) - 2 }}</span>
                        @endif
                    </div>
                @endif

                @if($canClaim)
                    <button type="button"
                        onclick="event.stopPropagation(); openClaimModal('{{ route('student.certificates.claim', $certificate->id) }}', '{{ addslashes($certificate->name) }}', {{ $certificate->min_score }}, {{ $student->score ?? 0 }}, '{{ $badge['color'] }}')"
                        class="w-full mt-3 py-2.5 rounded-xl font-bold text-white text-sm transition hover:opacity-90"
                        style="background: {{ $badge['color'] }};">
                        <i class="fas fa-hand-sparkles mr-1"></i> Réclamer ce certificat
                    </button>
                @endif

                <a href="{{ route('student.certificates.show', $certificate->id) }}" class="view-link mt-auto">Découvrir <i class="fas fa-arrow-right text-xs"></i></a>
            </div>
        </div>
    @empty
        <div class="col-span-3 text-center py-10 text-gray-400"><p>Aucune certification externe disponible.</p></div>
    @endforelse
</div>

<!-- Modale de confirmation de réclamation (partagée par toutes les cartes) -->
<div id="claimModalOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9998] hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl" style="animation: claim-modal-in 0.25s cubic-bezier(0.16,1,0.3,1);">
        <div id="claimModalIcon" class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 text-white">
            <i class="fas fa-gift"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Confirmer la réclamation</h3>
        <p id="claimModalText" class="text-sm text-gray-500 text-center mb-5"></p>
        <div class="flex gap-3">
            <button type="button" onclick="closeClaimModal()" class="flex-1 py-2.5 rounded-xl bg-gray-100 text-gray-600 font-semibold text-sm hover:bg-gray-200 transition">
                Annuler
            </button>
            <form id="claimModalForm" method="POST" class="flex-1">
                @csrf
                <button type="submit" id="claimModalConfirmBtn" class="w-full py-2.5 rounded-xl text-white font-semibold text-sm transition hover:opacity-90">
                    Confirmer
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes claim-modal-in { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
</style>

@endsection

@section('scripts')
<script>
    function openClaimModal(actionUrl, certName, cost, currentScore, color) {
        document.getElementById('claimModalForm').action = actionUrl;
        document.getElementById('claimModalText').innerHTML =
            `Réclamer <span class="font-semibold text-gray-800">${certName}</span> coûtera ` +
            `<span class="font-semibold text-gray-800">${cost} points</span>, déduits de votre score actuel (${currentScore} pts).`;
        document.getElementById('claimModalIcon').style.background = color;
        document.getElementById('claimModalConfirmBtn').style.background = color;

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
@endsection