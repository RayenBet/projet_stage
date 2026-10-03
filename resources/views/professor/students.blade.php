@extends('layouts.professor')

@section('title', 'Étudiants - Professeur')
@section('page_title', '👥 Étudiants par séance')
@section('page_subtitle', 'Présences et absences détaillées, séance par séance')
@section('active_students', 'active')

@section('content')

<style>
    .session-card {
        background: white; border: 1px solid #F1F5F9; border-radius: 22px;
        overflow: hidden; margin-bottom: 24px;
    }
    .session-card-header {
        padding: 22px 26px;
        background: linear-gradient(135deg, #0F172A 0%, #312E81 100%);
        color: white;
        position: relative;
        overflow: hidden;
    }
    .session-card-header::before {
        content: '';
        position: absolute; top: -50px; right: -30px; width: 160px; height: 160px;
        background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
        border-radius: 50%;
    }
    .session-meta-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.15);
        padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600;
    }

    .attend-col { padding: 20px; }
    .attend-col-title {
        display: flex; align-items: center; justify-content: space-between;
        font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 0.04em;
        margin-bottom: 14px; padding-bottom: 10px; border-bottom: 2px solid;
    }
    .col-present { border-color: #DCFCE7; }
    .col-present .attend-col-title { color: #15803D; border-color: #22C55E; }
    .col-late { border-color: #FEF3C7; }
    .col-late .attend-col-title { color: #B45309; border-color: #F59E0B; }
    .col-absent { border-color: #FEE2E2; }
    .col-absent .attend-col-title { color: #B91C1C; border-color: #EF4444; }

    .student-chip {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 12px; border-radius: 12px; margin-bottom: 8px;
        background: #F8FAFC; transition: background 0.15s ease;
    }
    .student-chip:hover { background: #F1F5F9; }
    .student-avatar-sm {
        width: 30px; height: 30px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 800; color: white; flex-shrink: 0;
    }
    .avatar-present { background: linear-gradient(135deg, #34D399, #059669); }
    .avatar-late { background: linear-gradient(135deg, #FBBF24, #D97706); }
    .avatar-absent { background: linear-gradient(135deg, #F87171, #DC2626); }

    .empty-col { text-align: center; padding: 24px 10px; color: #94A3B8; font-size: 13px; }
</style>

<!-- Filtres -->
<div class="filter-bar" style="background: white; border: 1px solid #F1F5F9; border-radius: 18px; padding: 20px; margin-bottom: 24px;">
    <form method="GET" action="{{ route('professor.students') }}" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Classe</label>
            <select name="class_id" class="form-select-prof" style="width: auto; min-width: 180px;">
                <option value="">Toutes les classes</option>
                @foreach($professorClasses as $classe)
                    <option value="{{ $classe->id }}" @selected(request('class_id') == $classe->id)>{{ $classe->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Du</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-select-prof" style="width: auto;">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Au</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-select-prof" style="width: auto;">
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" class="px-5 py-3 rounded-xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition">
                <i class="fas fa-filter mr-1"></i> Filtrer
            </button>
            @if(request('class_id') || request('date_from') || request('date_to'))
                <a href="{{ route('professor.students') }}" class="px-4 py-3 rounded-xl bg-gray-100 text-gray-600 font-semibold text-sm hover:bg-gray-200 transition">
                    Réinitialiser
                </a>
            @endif
        </div>
    </form>
</div>

@forelse($sessions as $session)
    @php
        $presentCount = $session->present_list->count();
        $lateCount = $session->late_list->count();
        $absentCount = $session->absent_list->count();
        $rate = $session->total_students > 0 ? round((($presentCount + $lateCount) / $session->total_students) * 100) : 0;
    @endphp
    <div class="session-card">
        <div class="session-card-header">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h4 class="font-extrabold text-lg mb-1">{{ $session->subject->name ?? 'Matière' }}</h4>
                    <div class="flex flex-wrap gap-2">
                        <span class="session-meta-chip"><i class="fas fa-calendar"></i> {{ optional($session->date)->format('d/m/Y') }}</span>
                        <span class="session-meta-chip"><i class="fas fa-users"></i> {{ $session->classe->name ?? 'N/A' }}</span>
                        <span class="session-meta-chip"><i class="fas fa-map-marker-alt"></i> {{ $session->campus === 'ons' ? 'ONS' : ($session->campus === 'mharza' ? 'Mharza' : $session->campus) }} • Salle {{ $session->room }}</span>
                        <span class="session-meta-chip"><i class="fas fa-clock"></i> {{ optional($session->start_time)->format('H:i') }} - {{ optional($session->end_time)->format('H:i') }}</span>
                    </div>
                </div>
                <div class="text-center bg-white/10 rounded-xl px-5 py-3 border border-white/15">
                    <p class="text-2xl font-extrabold">{{ $rate }}%</p>
                    <p class="text-[11px] text-indigo-200 uppercase tracking-wide">Taux présence</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
            <!-- Présents -->
            <div class="attend-col col-present">
                <div class="attend-col-title">
                    <span><i class="fas fa-check-circle mr-1"></i> Présents</span>
                    <span>{{ $presentCount }}</span>
                </div>
                @forelse($session->present_list as $student)
                    <div class="student-chip">
                        <div class="student-avatar-sm avatar-present">{{ strtoupper(substr($student->name, 0, 1) ?: 'E') }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $student->name ?: 'Étudiant' }}</p>
                            <p class="text-[11px] text-gray-400">{{ $student->matricule }}</p>
                        </div>
                    </div>
                @empty
                    <div class="empty-col">Aucun étudiant présent</div>
                @endforelse
            </div>

            <!-- En retard -->
            <div class="attend-col col-late">
                <div class="attend-col-title">
                    <span><i class="fas fa-clock mr-1"></i> En retard</span>
                    <span>{{ $lateCount }}</span>
                </div>
                @forelse($session->late_list as $student)
                    <div class="student-chip">
                        <div class="student-avatar-sm avatar-late">{{ strtoupper(substr($student->name, 0, 1) ?: 'E') }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $student->name ?: 'Étudiant' }}</p>
                            <p class="text-[11px] text-gray-400">{{ $student->matricule }}</p>
                        </div>
                    </div>
                @empty
                    <div class="empty-col">Aucun retard</div>
                @endforelse
            </div>

            <!-- Absents -->
            <div class="attend-col col-absent">
                <div class="attend-col-title">
                    <span><i class="fas fa-times-circle mr-1"></i> Absents</span>
                    <span>{{ $absentCount }}</span>
                </div>
                @forelse($session->absent_list as $student)
                    <div class="student-chip">
                        <div class="student-avatar-sm avatar-absent">{{ strtoupper(substr($student->name, 0, 1) ?: 'E') }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $student->name ?: 'Étudiant' }}</p>
                            <p class="text-[11px] text-gray-400">{{ $student->matricule }}</p>
                        </div>
                    </div>
                @empty
                    <div class="empty-col">Aucun absent 🎉</div>
                @endforelse
            </div>
        </div>
    </div>
@empty
    <div class="text-center py-16 text-gray-400 bg-white rounded-2xl border border-gray-100">
        <i class="fas fa-user-slash text-4xl mb-3"></i>
        <p>Aucune séance démarrée trouvée @if(request('class_id') || request('date_from') || request('date_to')) pour ces filtres @endif.</p>
    </div>
@endforelse

@if($sessions->hasPages())
    <div class="mt-2">
        {{ $sessions->links() }}
    </div>
@endif

@endsection