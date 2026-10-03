@extends('layouts.student')

@section('title', 'Mes présences - Étudiant')
@section('page_title', '🕓 Mes présences')
@section('page_subtitle', 'Historique complet de vos présences, retards et absences')
@section('active_history', 'active')

@section('content')

<style>
    .hist-hero {
        background: linear-gradient(135deg, #0F172A 0%, #312E81 55%, #4F46E5 100%);
        border-radius: 24px; padding: 36px; color: white; position: relative; overflow: hidden; margin-bottom: 32px;
    }
    .hist-hero::before {
        content: ''; position: absolute; top: -70px; right: -50px; width: 260px; height: 260px;
        background: radial-gradient(circle, rgba(255,255,255,0.13) 0%, transparent 70%); border-radius: 50%;
    }
    .hist-ring { width: 120px; height: 120px; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; flex-shrink: 0; }
    .hist-ring svg { position: absolute; top: 0; left: 0; transform: rotate(-90deg); }
    .hist-ring-inner {
        width: 92px; height: 92px; border-radius: 50%; background: rgba(255,255,255,0.08); backdrop-filter: blur(4px);
        display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 1;
    }
    .hist-stat-chip { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); border-radius: 14px; padding: 12px 16px; text-align: center; }

    .filter-bar { background: white; border: 1px solid #F1F5F9; border-radius: 18px; padding: 18px 20px; margin-bottom: 24px; }

    .date-group-label {
        font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #94A3B8;
        margin: 22px 0 10px; display: flex; align-items: center; gap: 10px;
    }
    .date-group-label::after { content: ''; flex: 1; height: 1px; background: #F1F5F9; }
    .date-group-label:first-child { margin-top: 0; }

    .hist-row {
        display: flex; align-items: center; gap: 16px;
        background: white; border: 1px solid #F1F5F9; border-radius: 16px;
        padding: 16px 20px; margin-bottom: 10px; transition: all 0.2s ease;
    }
    .hist-row:hover { border-color: #E2E8F0; box-shadow: 0 4px 12px rgba(15,23,42,0.05); transform: translateX(2px); }
    .hist-icon {
        width: 46px; height: 46px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;
    }
    .hist-icon.present { background: linear-gradient(135deg, #34D399, #059669); color: white; }
    .hist-icon.late { background: linear-gradient(135deg, #FBBF24, #D97706); color: white; }
    .hist-icon.absent { background: linear-gradient(135deg, #F87171, #DC2626); color: white; }
    .hist-badge {
        font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 999px;
        text-transform: uppercase; letter-spacing: 0.03em;
    }
    .hist-badge.present { background: #DCFCE7; color: #15803D; }
    .hist-badge.late { background: #FEF3C7; color: #B45309; }
    .hist-badge.absent { background: #FEE2E2; color: #B91C1C; }
</style>

@php
    $circumference = 2 * 3.14159 * 50;
@endphp

<!-- HERO avec jauge de présence globale -->
<div class="hist-hero">
    <div class="relative z-10 flex flex-col lg:flex-row items-center gap-8">
        <div class="hist-ring">
            <svg width="120" height="120">
                <circle cx="60" cy="60" r="50" stroke="rgba(255,255,255,0.15)" stroke-width="9" fill="none" />
                <circle cx="60" cy="60" r="50" stroke="#818CF8" stroke-width="9" fill="none"
                    stroke-dasharray="{{ $circumference }}"
                    stroke-dashoffset="{{ $circumference - ($circumference * $globalRate / 100) }}"
                    stroke-linecap="round" />
            </svg>
            <div class="hist-ring-inner">
                <span class="text-xl font-extrabold text-white">{{ $globalRate }}%</span>
                <span class="text-[10px] text-indigo-200">assiduité</span>
            </div>
        </div>

        <div class="flex-1 text-center lg:text-left">
            <p class="text-indigo-300 text-xs font-bold uppercase tracking-wide mb-1">Vue d'ensemble</p>
            <h3 class="text-2xl font-extrabold mb-2">Votre historique de présence</h3>
            <p class="text-indigo-200 max-w-lg text-sm">
                Sur l'ensemble de vos {{ $globalTotal }} séance{{ $globalTotal > 1 ? 's' : '' }} enregistrée{{ $globalTotal > 1 ? 's' : '' }},
                votre taux d'assiduité est de {{ $globalRate }}%.
            </p>
        </div>

        <div class="grid grid-cols-3 gap-3">
            <div class="hist-stat-chip">
                <p class="text-xl font-extrabold text-green-300">{{ $globalPresent }}</p>
                <p class="text-[11px] text-indigo-200">Présences</p>
            </div>
            <div class="hist-stat-chip">
                <p class="text-xl font-extrabold text-yellow-300">{{ $globalLate }}</p>
                <p class="text-[11px] text-indigo-200">Retards</p>
            </div>
            <div class="hist-stat-chip">
                <p class="text-xl font-extrabold text-red-300">{{ $globalAbsent }}</p>
                <p class="text-[11px] text-indigo-200">Absences</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="filter-bar">
    <form method="GET" action="{{ route('student.history') }}" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Statut</label>
            <select name="status" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="">Tous</option>
                <option value="present" @selected(request('status') == 'present')>Présent</option>
                <option value="late" @selected(request('status') == 'late')>Retard</option>
                <option value="absent" @selected(request('status') == 'absent')>Absent</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Du</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Au</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition">
            <i class="fas fa-filter mr-1"></i> Filtrer
        </button>
        @if(request('status') || request('date_from') || request('date_to'))
            <a href="{{ route('student.history') }}" class="px-4 py-2.5 rounded-xl bg-gray-100 text-gray-600 font-semibold text-sm hover:bg-gray-200 transition">
                Réinitialiser
            </a>
        @endif
    </form>
</div>

<!-- Liste groupée par date -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    @php $lastDate = null; @endphp

    @forelse($history as $attendance)
        @php
            $status = $attendance->status ?? 'absent';
            $sessionDate = optional($attendance->session)->date;
            $dateKey = $sessionDate ? \Carbon\Carbon::parse($sessionDate)->format('Y-m-d') : 'unknown';
        @endphp

        @if($dateKey !== $lastDate)
            <div class="date-group-label">
                <i class="fas fa-calendar-day"></i>
                {{ $sessionDate ? \Carbon\Carbon::parse($sessionDate)->locale('fr')->isoFormat('dddd D MMMM YYYY') : 'Date inconnue' }}
            </div>
            @php $lastDate = $dateKey; @endphp
        @endif

        <div class="hist-row">
            <div class="hist-icon {{ $status }}">
                <i class="fas fa-{{ $status == 'present' ? 'check' : ($status == 'late' ? 'clock' : 'times') }}"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900">{{ $attendance->session->subject->name ?? 'Matière' }}</p>
                <p class="text-sm text-gray-500">
                    {{ $attendance->session->classe->name ?? 'Classe' }}
                    @if($attendance->session)
                        • {{ optional($attendance->session->start_time)->format('H:i') }} - {{ optional($attendance->session->end_time)->format('H:i') }}
                    @endif
                </p>
            </div>
            <div class="text-right">
                <span class="hist-badge {{ $status }}">
                    @if($status == 'present') Présent
                    @elseif($status == 'late') Retard
                    @else Absent
                    @endif
                </span>
                @if($attendance->points_earned !== null)
                    <p class="text-xs mt-1 font-bold {{ $attendance->points_earned >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $attendance->points_earned >= 0 ? '+' : '' }}{{ $attendance->points_earned }} pts
                    </p>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-calendar-times text-4xl mb-3"></i>
            <p>Aucune présence trouvée @if(request('status') || request('date_from') || request('date_to')) pour ces filtres @endif.</p>
        </div>
    @endforelse

    @if($history->hasPages())
        <div class="mt-6">
            {{ $history->links() }}
        </div>
    @endif
</div>

@endsection