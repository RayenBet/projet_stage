@extends('layouts.student')

@section('title', 'Classement - Étudiant')
@section('page_title', '🏆 Classement de la classe')
@section('page_subtitle', 'Les 3 premiers de chaque classe bénéficient d\'une réduction sur leurs frais d\'éducation')
@section('active_ranking', 'active')

@section('content')

<style>
    .ranking-hero {
        background: linear-gradient(135deg, #0F172A 0%, #78350F 55%, #B45309 100%);
        border-radius: 24px; padding: 36px; color: white; position: relative; overflow: hidden; margin-bottom: 32px;
    }
    .ranking-hero::before {
        content: ''; position: absolute; top: -70px; right: -50px; width: 260px; height: 260px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%); border-radius: 50%;
    }

    .podium-wrap {
        display: flex; align-items: flex-end; justify-content: center; gap: 16px;
        margin: 10px 0 8px;
    }
    .podium-card {
        border-radius: 20px 20px 0 0;
        padding: 24px 20px 20px;
        text-align: center;
        position: relative;
        color: white;
        width: 220px;
    }
    .podium-card.rank-1 { background: linear-gradient(160deg, #FBBF24, #B45309); min-height: 240px; order: 2; box-shadow: 0 20px 40px -12px rgba(180,83,9,0.4); }
    .podium-card.rank-2 { background: linear-gradient(160deg, #CBD5E1, #64748B); min-height: 220px; order: 1; }
    .podium-card.rank-3 { background: linear-gradient(160deg, #FDBA74, #C2410C); min-height: 220px; order: 3; }

    .podium-crown { font-size: 28px; margin-bottom: 4px; }
    .podium-avatar {
        width: 56px; height: 56px; border-radius: 50%; background: rgba(255,255,255,0.25);
        display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px;
        margin: 0 auto 10px; border: 2px solid rgba(255,255,255,0.5);
    }
    .discount-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: rgba(255,255,255,0.25); backdrop-filter: blur(4px);
        padding: 5px 12px; border-radius: 999px; font-weight: 800; font-size: 13px; margin-top: 8px;
    }

    .rank-row {
        display: flex; align-items: center; gap: 16px;
        background: white; border: 1px solid #F1F5F9; border-radius: 16px;
        padding: 14px 18px; margin-bottom: 10px; transition: all 0.2s ease;
    }
    .rank-row:hover { border-color: #E2E8F0; box-shadow: 0 4px 12px rgba(15,23,42,0.05); }
    .rank-row.is-me { border-color: #6366F1; background: #EEF2FF; }
    .rank-num {
        width: 34px; height: 34px; border-radius: 10px; background: #F1F5F9; color: #64748B;
        display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; flex-shrink: 0;
    }
    .rank-avatar {
        width: 40px; height: 40px; border-radius: 12px; background: #EEF2FF; color: #4F46E5;
        display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; flex-shrink: 0;
    }
</style>

<!-- HERO -->
<div class="ranking-hero">
    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="text-center md:text-left">
            <p class="text-amber-200 text-xs font-bold uppercase tracking-wide mb-1">{{ $student->classe->name ?? 'Votre classe' }}</p>
            <h3 class="text-2xl font-extrabold mb-2">Classement des étudiants</h3>
            <p class="text-amber-100 max-w-lg text-sm">
                Les 3 meilleurs scores de la classe obtiennent une réduction exclusive sur les frais d'éducation.
                Continuez à assister à vos séances pour grimper dans le classement !
            </p>
        </div>
        <div class="text-center bg-white/10 rounded-2xl p-5 backdrop-blur-sm border border-white/15 min-w-[130px]">
            <p class="text-3xl font-extrabold">{{ $myRank ?? '-' }}</p>
            <p class="text-xs text-amber-100 uppercase tracking-wide">Votre rang</p>
        </div>
    </div>
</div>

<!-- PODIUM TOP 3 -->
@if($podium->count() > 0)
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 mb-8">
    <h4 class="font-bold text-gray-900 text-center mb-6 flex items-center justify-center gap-2">
        <i class="fas fa-crown text-amber-500"></i> Podium & bonifications
    </h4>

    <div class="podium-wrap flex-wrap">
        @foreach($podium as $index => $s)
            @php
                $position = $index + 1;
                $isMe = $s->id === $student->id;
                $initials = strtoupper(substr($s->first_name ?? 'E', 0, 1) . substr($s->last_name ?? 'T', 0, 1));
            @endphp
            <div class="podium-card rank-{{ $position }}" style="{{ $isMe ? 'outline: 3px solid #4F46E5; outline-offset: 3px;' : '' }}">
                <div class="podium-crown">
                    @if($position == 1) 🥇 @elseif($position == 2) 🥈 @else 🥉 @endif
                </div>
                <div class="podium-avatar">{{ $initials }}</div>
                <p class="font-extrabold text-sm leading-tight">{{ $s->first_name }} {{ $s->last_name }}</p>
                <p class="text-xs opacity-80 mt-1">{{ $s->score }} points</p>
                <div class="discount-badge">
                    <i class="fas fa-tag"></i> -{{ $discounts[$position] }}% frais
                </div>
            </div>
        @endforeach
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">
        <i class="fas fa-info-circle mr-1"></i>
        Réduction appliquée sur les frais d'éducation du prochain semestre, sous réserve de validation administrative.
    </p>
</div>
@endif

<!-- CLASSEMENT COMPLET -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
        <i class="fas fa-list-ol text-indigo-600"></i> Classement complet
    </h4>

    @forelse($others as $index => $s)
        @php
            $rankNum = $index + 4;
            $isMe = $s->id === $student->id;
            $initials = strtoupper(substr($s->first_name ?? 'E', 0, 1) . substr($s->last_name ?? 'T', 0, 1));
        @endphp
        <div class="rank-row {{ $isMe ? 'is-me' : '' }}">
            <div class="rank-num">#{{ $rankNum }}</div>
            <div class="rank-avatar">{{ $initials }}</div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900 text-sm">
                    {{ $s->first_name }} {{ $s->last_name }}
                    @if($isMe) <span class="text-indigo-600 text-xs font-bold">(Vous)</span> @endif
                </p>
                <p class="text-xs text-gray-400">{{ $s->matricule }}</p>
            </div>
            <div class="text-right">
                <p class="font-bold text-gray-900">{{ $s->score }} pts</p>
            </div>
        </div>
    @empty
        <p class="text-center text-gray-400 py-6">Aucun autre étudiant dans cette classe pour le moment.</p>
    @endforelse
</div>

@endsection