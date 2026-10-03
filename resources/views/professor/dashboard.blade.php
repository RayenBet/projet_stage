@extends('layouts.professor')

@section('title', 'Dashboard - Professeur')
@section('page_title', '👨‍🏫 Tableau de bord Professeur')
@section('page_subtitle', 'Gérez vos séances et suivez les présences en temps réel')
@section('active_dashboard', 'active')

@section('content')

<style>
    .prof-hero {
        background: linear-gradient(135deg, #0F172A 0%, #064E3B 55%, #059669 100%);
        border-radius: 24px;
        padding: 34px 36px;
        position: relative;
        overflow: hidden;
        color: white;
        margin-bottom: 32px;
    }
    .prof-hero::before {
        content: '';
        position: absolute; top: -70px; right: -50px; width: 260px; height: 260px;
        background: radial-gradient(circle, rgba(255,255,255,0.13) 0%, transparent 70%);
        border-radius: 50%;
    }
    .prof-hero::after {
        content: '';
        position: absolute; bottom: -90px; left: 20%; width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(52,211,153,0.18) 0%, transparent 70%);
        border-radius: 50%;
    }
    .prof-avatar {
        width: 84px; height: 84px; border-radius: 22px;
        background: rgba(255,255,255,0.12); backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,0.25);
        display: flex; align-items: center; justify-content: center;
        font-size: 30px; font-weight: 800; color: white; flex-shrink: 0;
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }
    .prof-info-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);
        padding: 5px 13px; border-radius: 999px; font-size: 12px; font-weight: 600;
    }

    .section-header-pro {
        display: flex; align-items: center; gap: 12px; margin-bottom: 24px;
    }
    .section-icon-badge {
        width: 42px; height: 42px; border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0;
    }
</style>

@php
    $profFullName = trim(($professor->first_name ?? '') . ' ' . ($professor->last_name ?? '')) ?: 'Professeur';
    $profInitials = strtoupper(substr($professor->first_name ?? 'P', 0, 1) . substr($professor->last_name ?? 'R', 0, 1));
@endphp

<!-- Carte d'identité professeur -->
<div class="prof-hero">
    <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-6">
        <div class="prof-avatar">{{ $profInitials }}</div>
        <div class="text-center md:text-left flex-1">
            <p class="text-emerald-300 text-xs font-bold uppercase tracking-wide mb-1">Espace professeur</p>
            <h3 class="text-2xl font-extrabold mb-2">Bonjour, {{ $profFullName }} 👋</h3>
            <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-3">
                @if($professor->specialty)
                    <span class="prof-info-chip"><i class="fas fa-chalkboard-teacher"></i> {{ $professor->specialty }}</span>
                @endif
                @if($professor->department)
                    <span class="prof-info-chip"><i class="fas fa-building"></i> {{ $professor->department }}</span>
                @endif
            </div>
            <p class="text-emerald-100 text-sm">
                <i class="fas fa-envelope mr-1"></i> {{ Auth::user()->email }}
            </p>
        </div>
    </div>
</div>

<!-- Section: Création de Séance -->
<div class="card-prof mb-8">
    <div class="section-header-pro">
        <div class="section-icon-badge bg-emerald-50 text-emerald-600">
            <i class="fas fa-plus-circle"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-900">Créer une nouvelle séance</h3>
            <p class="text-sm text-gray-400">Planifiez et démarrez un cours pour l'une de vos classes</p>
        </div>
    </div>
    
    <form id="sessionForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @csrf
        
        <!-- CAMPUS (Boutons PRO - Gardé tel quel) -->
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1.5">Campus</label>
            <div class="campus-selector flex gap-2">
                <button type="button" class="campus-btn active px-4 py-2 rounded-lg bg-emerald-600 text-white font-medium text-sm transition" data-campus="ons" onclick="selectCampus(this, 'ons')">
                    <i class="fas fa-university mr-1"></i> ONS
                </button>
                <button type="button" class="campus-btn px-4 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium text-sm hover:bg-gray-300 transition" data-campus="mharza" onclick="selectCampus(this, 'mharza')">
                    <i class="fas fa-school mr-1"></i> Mharza
                </button>
            </div>
            <input type="hidden" name="campus" id="campusInput" value="ons">
        </div>
        
        <!-- SALLE (dynamique, filtrée par campus sélectionné) -->
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1.5">Salle</label>
            <select name="room" id="roomSelect" class="form-select-prof w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white" required>
                <option value="">-- Choisir une salle --</option>
                @foreach($rooms ?? [] as $room)
                    <option value="{{ $room->name }}" data-campus="{{ $room->campus }}">{{ $room->name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- MATIÈRE -->
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1.5">Matière</label>
            <select name="subject_id" class="form-select-prof w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white" required>
                <option value="">-- Sélectionner une matière --</option>
                @foreach($subjects ?? [] as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- CLASSE -->
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1.5">Classe</label>
            <select name="class_id" class="form-select-prof w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white" required>
                <option value="">-- Sélectionner une classe --</option>
                @foreach($classes ?? [] as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- DATE -->
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1.5">Date</label>
            <input type="date" name="date" class="form-select-prof w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white" required>
        </div>
        
        <!-- HEURE DÉBUT -->
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1.5">Heure début</label>
            <input type="time" name="start_time" class="form-select-prof w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white" required>
        </div>
        
        <!-- DURÉE -->
        <div>
            <label class="text-sm font-medium text-gray-700 block mb-1.5">Durée (minutes)</label>
            <select name="duration" class="form-select-prof w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white">
                <option value="45">45 min</option>
                <option value="60">1h</option>
                <option value="90">1h30</option>
                <option value="120" selected>2h</option>
                <option value="180">3h</option>
            </select>
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="btn-start-session w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
                <i class="fas fa-play"></i>
                Démarrer la séance
            </button>
        </div>
    </form>
</div>

<!-- Section: Séance en Cours -->
@if(isset($activeSession) && $activeSession)
<div class="card-prof mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100" id="activeSession">
    <div class="flex flex-wrap items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="section-icon-badge bg-red-50 text-red-600">
                <i class="fas fa-satellite-dish"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Séance en cours</h3>
            <span class="live-indicator active inline-flex items-center gap-2 bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold" id="sessionStatus">
                <span class="live-dot active w-2 h-2 bg-red-600 rounded-full animate-pulse"></span>
                En direct
            </span>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-500" id="sessionInfo">{{ $activeSession->subject->name ?? 'Matière' }} • Salle {{ $activeSession->room }}</span>
            <span class="text-sm font-bold text-emerald-600" id="timerDisplay">{{ \Carbon\Carbon::parse($activeSession->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($activeSession->end_time)->format('H:i') }}</span>
            <button type="button" onclick="endActiveSession({{ $activeSession->id }})" class="btn-end-session px-4 py-2 rounded-xl bg-red-50 text-red-600 font-semibold text-sm hover:bg-red-100 transition inline-flex items-center gap-2">
                <i class="fas fa-stop"></i> Terminer la séance
            </button>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-50 rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold text-gray-900" id="totalStudents">{{ $sessionStats['total'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">👥 Total étudiants</p>
        </div>
        <div class="bg-green-50 rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold text-green-600" id="presentCount">{{ $sessionStats['present'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">✅ Présents</p>
        </div>
        <div class="bg-yellow-50 rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold text-yellow-600" id="lateCount">{{ $sessionStats['late'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">⏰ Retards</p>
        </div>
        <div class="bg-red-50 rounded-2xl p-4 text-center">
            <p class="text-2xl font-bold text-red-600" id="absentCount">{{ $sessionStats['absent'] ?? 0 }}</p>
            <p class="text-xs text-gray-500">❌ Absents</p>
        </div>
    </div>

    <!-- Tableau des présences -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">#</th>
                    <th class="px-6 py-3">Étudiant</th>
                    <th class="px-6 py-3">Classe</th>
                    <th class="px-6 py-3">Statut</th>
                    <th class="px-6 py-3">Heure d'arrivée</th>
                    <th class="px-6 py-3">Points</th>
                </tr>
            </thead>
            <tbody id="attendanceBody">
                @forelse($attendances ?? [] as $attendance)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="student-avatar w-8 h-8 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center font-bold text-xs">{{ $attendance->student->initials ?? 'XX' }}</div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $attendance->student->full_name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $attendance->student->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">{{ $attendance->student->classe->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge status-{{ $attendance->status }} px-2 py-1 rounded-full text-xs font-semibold inline-block
                            @if($attendance->status == 'present') bg-green-100 text-green-700
                            @elseif($attendance->status == 'late') bg-yellow-100 text-yellow-700
                            @elseif($attendance->status == 'absent') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700 @endif
                        ">
                            @if($attendance->status == 'present') ✅ Présent
                            @elseif($attendance->status == 'late') ⏰ Retard
                            @elseif($attendance->status == 'absent') ❌ Absent
                            @else Annulé
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $attendance->arrival_time ? \Carbon\Carbon::parse($attendance->arrival_time)->format('H:i:s') : '-' }}</td>
                    <td class="px-6 py-4 font-bold {{ $attendance->points_earned >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $attendance->points_earned >= 0 ? '+' : '' }}{{ $attendance->points_earned }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">Aucun étudiant enregistré pour cette séance</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card-prof mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
    <div class="text-center py-12">
        <i class="fas fa-clock text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-semibold text-gray-600">Aucune séance en cours</h3>
        <p class="text-gray-400 text-sm">Créez une nouvelle séance ci-dessus pour commencer</p>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    // Terminer la séance active en cours (adapté à n'importe quelle séance,
    // via son id passé en paramètre)
    function endActiveSession(sessionId) {
        if (!confirm('Terminer cette séance ? Les présences enregistrées seront figées.')) {
            return;
        }

        fetch(`/professor/session/${sessionId}/end`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof showToast === 'function') {
                    showToast('Séance terminée.', 'success');
                }
                setTimeout(() => window.location.reload(), 800);
            } else {
                if (typeof showToast === 'function') {
                    showToast(data.error || 'Impossible de terminer la séance.', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof showToast === 'function') {
                showToast('Une erreur réseau ou serveur est survenue.', 'error');
            }
        });
    }

    // Gestion des boutons Campus
    function selectCampus(btn, campus) {
        document.querySelectorAll('.campus-btn').forEach(b => {
            b.classList.remove('active', 'bg-emerald-600', 'text-white');
            b.classList.add('bg-gray-200', 'text-gray-700');
        });
        btn.classList.remove('bg-gray-200', 'text-gray-700');
        btn.classList.add('active', 'bg-emerald-600', 'text-white');
        document.getElementById('campusInput').value = campus;
        filterRoomsByCampus(campus);
    }

    // N'affiche que les salles du campus sélectionné dans le menu déroulant
    function filterRoomsByCampus(campus) {
        const select = document.getElementById('roomSelect');
        if (!select) return;

        let hasVisibleSelection = false;
        Array.from(select.options).forEach(option => {
            if (!option.value) return; // garde toujours l'option vide visible
            const matches = option.dataset.campus === campus;
            option.hidden = !matches;
            if (matches && option.selected) hasVisibleSelection = true;
        });

        if (!hasVisibleSelection) {
            select.value = '';
        }
    }

    // Filtre initial selon le campus actif par défaut (ONS)
    filterRoomsByCampus(document.getElementById('campusInput').value);

    // Date par défaut
    document.querySelector('input[name="date"]').valueAsDate = new Date();

    // Gestion du formulaire de séance
    document.getElementById('sessionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        const btn = this.querySelector('.btn-start-session');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création...';
        
        fetch('{{ route("professor.session.create") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Démarrage...';

                // La séance est créée en statut "pending" ; on la démarre
                // immédiatement pour qu'elle devienne active et visible.
                return fetch(`/professor/session/${data.session.id}/start`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(startData => {
                    if (startData.success) {
                        btn.innerHTML = '<i class="fas fa-check"></i> Séance démarrée !';
                        btn.style.background = 'linear-gradient(135deg, #059669, #10B981)';
                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        // La séance a été créée mais n'a pas pu démarrer
                        // (ex : une autre séance est déjà active).
                        btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Erreur';
                        btn.style.background = 'linear-gradient(135deg, #DC2626, #EF4444)';
                        if (typeof showToast === 'function') {
                            showToast(startData.debug || startData.error || 'Séance créée mais impossible à démarrer.', 'error');
                        }
                        setTimeout(() => window.location.reload(), 2000);
                    }
                });
            } else {
                btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Erreur';
                btn.style.background = 'linear-gradient(135deg, #DC2626, #EF4444)';
                if (typeof showToast === 'function') {
                    const firstError = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Erreur inconnue');
                    showToast(firstError, 'error');
                }
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-play"></i> Démarrer la séance';
                    btn.style.background = '';
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Erreur réseau';
            if (typeof showToast === 'function') {
                showToast('Une erreur réseau ou serveur est survenue.', 'error');
            }
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-play"></i> Démarrer la séance';
                btn.style.background = '';
            }, 3000);
        });
    });

    // ============================================================
    // TEMPS RÉEL — écoute Laravel Reverb (protocole Pusher) en direct
    // Remplace l'ancien simulateur de données aléatoires.
    // ============================================================
    @if(isset($activeSession) && $activeSession)
    (function () {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js';
        script.onload = initRealtime;
        document.head.appendChild(script);

        function initRealtime() {
            const pusher = new Pusher('{{ env('REVERB_APP_KEY') }}', {
                cluster: 'mt1',
                wsHost: '{{ env('REVERB_HOST', 'localhost') }}',
                wsPort: {{ env('REVERB_PORT', 8080) }},
                wssPort: {{ env('REVERB_PORT', 8080) }},
                forceTLS: false,
                enabledTransports: ['ws'],
                disableStats: true,
            });

            const channel = pusher.subscribe('session.{{ $activeSession->id }}');

            channel.bind('attendance.marked', function (data) {
                // Mise à jour des compteurs globaux
                const s = data.stats;
                document.getElementById('totalStudents').textContent = s.total;
                document.getElementById('presentCount').textContent = s.present;
                document.getElementById('lateCount').textContent = s.late;
                document.getElementById('absentCount').textContent = s.absent;

                if (typeof showToast === 'function') {
                    const labels = { present: 'présent ✅', late: 'en retard ⏰', absent: 'absent ❌' };
                    showToast(`${data.student_name} marqué ${labels[data.status] || data.status}`, 'info');
                }
            });

            window.addEventListener('beforeunload', () => pusher.disconnect());
        }
    })();
    @endif
</script>

@endsection