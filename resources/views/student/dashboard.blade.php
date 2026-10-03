@extends('layouts.student')

@section('title', 'Dashboard - Étudiant')
@section('page_title', '👋 Bonjour, ' . (Auth::user()->student->first_name ?? 'Étudiant') . ' !')
@section('page_subtitle', 'Bienvenue sur votre espace étudiant')
@section('active_dashboard', 'active')

@section('content')

<style>
    .profile-banner {
        background: linear-gradient(135deg, #0F172A 0%, #312E81 55%, #4F46E5 100%);
        border-radius: 24px;
        padding: 36px;
        position: relative;
        overflow: hidden;
        color: white;
        margin-bottom: 32px;
    }
    .profile-banner::before {
        content: '';
        position: absolute; top: -80px; right: -60px; width: 280px; height: 280px;
        background: radial-gradient(circle, rgba(255,255,255,0.14) 0%, transparent 70%);
        border-radius: 50%;
    }
    .profile-banner::after {
        content: '';
        position: absolute; bottom: -100px; left: 15%; width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(129,140,248,0.18) 0%, transparent 70%);
        border-radius: 50%;
    }
    .profile-avatar {
        width: 92px; height: 92px; border-radius: 24px;
        background: rgba(255,255,255,0.12); backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,0.25);
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; font-weight: 800; color: white; flex-shrink: 0;
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }
    .rank-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: linear-gradient(135deg, #FBBF24, #F59E0B);
        color: #78350F; font-weight: 800; font-size: 13px;
        padding: 6px 14px; border-radius: 999px;
    }
    .info-pill {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 16px;
        padding: 16px 18px;
        backdrop-filter: blur(4px);
        transition: background 0.2s ease;
    }
    .info-pill:hover { background: rgba(255,255,255,0.13); }
    .info-pill .label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: rgba(255,255,255,0.6); font-weight: 700; }
    .info-pill .value { font-size: 15px; font-weight: 700; color: white; margin-top: 4px; }
</style>

@php
    $student = Auth::user()->student;
    $initials = $student ? strtoupper(substr($student->first_name ?? 'E', 0, 1) . substr($student->last_name ?? 'T', 0, 1)) : 'ET';
@endphp

<!-- Carte d'identité étudiant (en tête du dashboard) -->
<div class="profile-banner">
    <div class="relative z-10">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-6 mb-8">
            <div class="profile-avatar">{{ $initials }}</div>
            <div class="text-center md:text-left flex-1">
                <p class="text-indigo-300 text-xs font-bold uppercase tracking-wide mb-1">Carte d'identité étudiant</p>
                <h3 class="text-2xl font-extrabold mb-2">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</h3>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                    <span class="rank-chip"><i class="fas fa-trophy"></i> {{ $rank ?? 1 }}{{ ($rank ?? 1) == 1 ? 'er' : 'ème' }} de la classe</span>
                    <span class="text-indigo-200 text-sm"><i class="fas fa-star mr-1"></i> <span data-counter="{{ $student->score ?? 0 }}">0</span> points</span>
                </div>
                <p class="text-indigo-200 text-sm mt-2">
                    <i class="fas fa-id-badge mr-1"></i> {{ $student->matricule ?? 'N/A' }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-envelope mr-1"></i> {{ Auth::user()->email }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="info-pill">
                <p class="label"><i class="fas fa-graduation-cap mr-1"></i> Spécialité</p>
                <p class="value">{{ $student->classe->name ?? 'Non défini' }}</p>
            </div>
            <div class="info-pill">
                <p class="label"><i class="fas fa-layer-group mr-1"></i> Niveau</p>
                <p class="value">{{ $student->classe->level ?? 'Non défini' }}</p>
            </div>
            <div class="info-pill">
                <p class="label"><i class="fas fa-building mr-1"></i> Département</p>
                <p class="value">{{ $student->classe->department ?? 'Non défini' }}</p>
            </div>
            <div class="info-pill">
                <p class="label"><i class="fas fa-map-marker-alt mr-1"></i> Campus</p>
                <p class="value">{{ $student->classe->campus ?? 'Non défini' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 text-xl">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $totalSessions ?? 0 }}</p>
                <p class="text-sm text-gray-500">Séances assistées</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 text-xl">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $presentCount ?? 0 }}</p>
                <p class="text-sm text-gray-500">Présences</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center text-yellow-600 text-xl">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $lateCount ?? 0 }}</p>
                <p class="text-sm text-gray-500">Retards</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 text-xl">
                <i class="fas fa-certificate"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ isset($unlockedCertificates) ? $unlockedCertificates->count() : 0 }}</p>
                <p class="text-sm text-gray-500">Certificats obtenus</p>
            </div>
        </div>
    </div>
</div>

<!-- Score & Progression -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Score -->
    <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm flex flex-col items-center">
        <p class="text-sm font-medium text-gray-500 mb-4">🏆 Score Total</p>
        @php
            $studentScore = Auth::user()->student->score ?? 0;
            $scoreMax = ($certificates ?? collect())->first()->min_score ?? 100;
            $scorePercent = $scoreMax > 0 ? min(100, ($studentScore / $scoreMax) * 100) : 0;
            $scoreDeg = $scorePercent * 3.6;
        @endphp
        <div class="score-circle" style="background: conic-gradient(#4F46E5 0deg, #4F46E5 {{ $scoreDeg }}deg, #E2E8F0 {{ $scoreDeg }}deg, #E2E8F0 360deg);">
            <div class="score-circle-inner">
                <span class="score-number" data-counter="{{ $studentScore }}">0</span>
                <span class="text-xs text-gray-400">points</span>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-3">
            <span class="badge-rank gold">🥇 {{ $rank ?? 1 }}er</span>
            <span class="text-sm text-gray-500">dans {{ Auth::user()->student->classe->name ?? 'la classe' }}</span>
        </div>
        <div class="w-full mt-4 pt-4 border-t border-gray-100">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Prochain certificat:</span>
                <span class="font-semibold text-indigo-600">
                    @if(isset($certificates) && $certificates->first())
                        {{ $certificates->first()->name ?? 'Certificat' }}
                    @else
                        Aucun
                    @endif
                </span>
            </div>
            @if(isset($certificates) && $certificates->first())
            <div class="progress-bar-cert">
                <div class="fill bg-indigo-600" style="width: {{ min(100, (Auth::user()->student->score ?? 0) / max(1, $certificates->first()->min_score ?? 1) * 100) }}%;"></div>
            </div>
            <p class="text-xs text-gray-400 text-right mt-1">{{ Auth::user()->student->score ?? 0 }}/{{ $certificates->first()->min_score ?? 0 }} pts</p>
            @endif
        </div>
    </div>

    <!-- Dernières présences -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm lg:col-span-2">
        <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-history text-indigo-600"></i>
            Historique des présences
        </h4>
        <div class="history-timeline max-h-64 overflow-y-auto">
            @forelse($history ?? [] as $attendance)
            <div class="timeline-item {{ $attendance->status }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">{{ $attendance->session->subject->name ?? 'Matière' }}</p>
                        <p class="text-sm text-gray-500">{{ $attendance->session->classe->name ?? 'Classe' }} • Salle {{ $attendance->session->room ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="status-badge status-{{ $attendance->status }}">
                            @if($attendance->status == 'present') ✅ Présent
                            @elseif($attendance->status == 'late') ⏰ Retard
                            @elseif($attendance->status == 'absent') ❌ Absent
                            @else Annulé
                            @endif
                        </span>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($attendance->created_at)->format('d/m/Y • H:i') }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <p>Aucune présence enregistrée</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    setInterval(() => {
        document.querySelectorAll('.progress-bar-cert .fill').forEach(bar => {
            const width = parseFloat(bar.style.width);
            const newWidth = Math.min(100, width + Math.random() * 2);
            bar.style.width = newWidth + '%';
        });
    }, 10000);
</script>
@endsection