@extends('layouts.professor')

@section('title', 'Historique des séances - Professeur')
@section('page_title', '📅 Historique des séances')
@section('page_subtitle', 'Historique complet de vos séances programmées')
@section('active_sessions', 'active')

@section('content')

<style>
    .filter-bar {
        background: white; border: 1px solid #F1F5F9; border-radius: 18px;
        padding: 20px; margin-bottom: 24px;
    }
    .session-row-status {
        font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 999px;
        text-transform: uppercase; letter-spacing: 0.03em;
    }
    .status-completed { background: #DCFCE7; color: #15803D; }
    .status-active { background: #DBEAFE; color: #1D4ED8; }
    .status-pending { background: #FEF3C7; color: #B45309; }
    .status-cancelled { background: #FEE2E2; color: #B91C1C; }
    .status-missed { background: #F1F5F9; color: #64748B; }
    .status-overdue { background: #FFEDD5; color: #C2410C; }

    .attend-bar-track { width: 100%; height: 6px; background: #F1F5F9; border-radius: 999px; overflow: hidden; }
    .attend-bar-fill { height: 100%; background: linear-gradient(90deg, #10B981, #059669); border-radius: 999px; }
</style>

<!-- Filtres + export -->
<div class="filter-bar">
    <form method="GET" action="{{ route('professor.sessions') }}" class="flex flex-wrap items-end gap-4">
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
            @if(request('date_from') || request('date_to'))
                <a href="{{ route('professor.sessions') }}" class="px-4 py-3 rounded-xl bg-gray-100 text-gray-600 font-semibold text-sm hover:bg-gray-200 transition">
                    Réinitialiser
                </a>
            @endif
        </div>

        <div class="ml-auto">
            <a href="{{ route('professor.sessions.export', request()->query()) }}"
               class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border-2 border-emerald-600 text-emerald-700 font-semibold text-sm hover:bg-emerald-50 transition">
                <i class="fas fa-file-csv"></i> Exporter en CSV
            </a>
        </div>
    </form>
</div>

<!-- Liste des séances -->
<div class="card-prof p-0 overflow-hidden">
    @if($sessions->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-calendar-times text-4xl mb-3"></i>
            <p>Aucune séance trouvée @if(request('date_from') || request('date_to')) pour cette période @endif.</p>
        </div>
    @else
        <table class="table-attendance">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Matière</th>
                    <th>Classe</th>
                    <th>Campus / Salle</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Présence</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                    @php
                        $total = $session->total_attendances;
                        $present = $session->present_count;
                        $rate = $total > 0 ? round(($present / $total) * 100) : 0;
                    @endphp
                    <tr>
                        <td>
                            <p class="font-semibold text-gray-900">{{ optional($session->date)->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-400 capitalize">{{ optional($session->date)->locale('fr')->isoFormat('dddd') }}</p>
                        </td>
                        <td>
                            <p class="font-semibold text-gray-900">{{ $session->subject->name ?? 'N/A' }}</p>
                        </td>
                        <td>{{ $session->classe->name ?? 'N/A' }}</td>
                        <td>
                            <span class="text-gray-700">{{ $session->campus === 'ons' ? 'ONS' : ($session->campus === 'mharza' ? 'Mharza' : $session->campus) }}</span>
                            <span class="text-gray-400 text-xs block">Salle {{ $session->room }}</span>
                        </td>
                        <td>{{ optional($session->start_time)->format('H:i') }}</td>
                        <td>{{ optional($session->end_time)->format('H:i') }}</td>
                        <td style="min-width: 140px;">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold text-gray-700">{{ $present }}/{{ $total }}</span>
                                <span class="text-gray-400">{{ $rate }}%</span>
                            </div>
                            <div class="attend-bar-track">
                                <div class="attend-bar-fill" style="width: {{ $rate }}%;"></div>
                            </div>
                        </td>
                        <td>
                            <span class="session-row-status status-{{ $session->effective_status }}">
                                @switch($session->effective_status)
                                    @case('completed') Terminée @break
                                    @case('active') En cours @break
                                    @case('pending') Planifiée @break
                                    @case('cancelled') Annulée @break
                                    @case('missed') Manquée @break
                                    @case('overdue') À clôturer @break
                                    @default {{ $session->effective_status }}
                                @endswitch
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $sessions->links() }}
        </div>
    @endif
</div>

@endsection