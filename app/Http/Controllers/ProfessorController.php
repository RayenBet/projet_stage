<?php

namespace App\Http\Controllers;


use App\Models\Professor;
use App\Models\Session;
use App\Models\Classe;
use App\Models\Subject;
use App\Models\Room;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\AttendanceMarked;
use App\Events\SessionStarted;

class ProfessorController extends Controller
{


    public function dashboard()
    {
        $user = Auth::user();
        $professor = $user->professor;
        
        // Séance en cours (active)
        $activeSession = Session::where('professor_id', $professor->id)
            ->where('status', 'active')
            ->with(['classe', 'subject'])
            ->first();
        
        // Statistiques de la séance active
        $sessionStats = null;
        $attendances = collect();
        if ($activeSession) {
            $attendances = Attendance::where('session_id', $activeSession->id)
                ->with('student')
                ->get();
            
            $total = $attendances->count();
            $present = $attendances->where('status', 'present')->count();
            $late = $attendances->where('status', 'late')->count();
            $absent = $attendances->where('status', 'absent')->count();
            
            $sessionStats = [
                'total' => $total,
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0
            ];
        }
        
        // Historique des séances
        $history = Session::where('professor_id', $professor->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->with(['classe', 'subject'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->limit(10)
            ->get();
        
        // Classes disponibles
        $classes = Classe::all();
        $subjects = Subject::all();
        $rooms = Room::orderBy('campus')->orderBy('name')->get();
        
        return view('professor.dashboard', compact(
            'professor',
            'activeSession',
            'attendances',
            'sessionStats',
            'history',
            'classes',
            'subjects',
            'rooms'
        ));
    }

    public function createSession(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'campus' => 'required|in:ons,mharza',
            'room' => 'required|string|max:50|exists:rooms,name',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'duration' => 'required|in:45,60,90,120,180'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Formulaire invalide.',
                'errors' => $validator->errors()
            ], 422);
        }

        $professor = Auth::user()->professor;
        
        // Calcul de l'heure de fin
        $start = \Carbon\Carbon::parse($request->date . ' ' . $request->start_time);
        $end = $start->copy()->addMinutes((int) $request->duration);

        $session = Session::create([
            'professor_id' => $professor->id,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'room' => $request->room,
            'campus' => $request->campus,
            'date' => $request->date,
            'start_time' => $start,
            'end_time' => $end,
            'duration' => $request->duration,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Séance créée avec succès',
            'session' => $session
        ]);
    }

    public function startSession($id)
    {
        $session = Session::findOrFail($id);
        $professor = Auth::user()->professor;
        
        if ($session->professor_id !== $professor->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        if ($session->status !== 'pending') {
            return response()->json([
                'error' => 'Cette séance ne peut pas être démarrée (statut actuel : ' . $session->status . ').'
            ], 422);
        }

        $alreadyActive = Session::where('professor_id', $professor->id)
            ->where('status', 'active')
            ->where('id', '!=', $session->id)
            ->first();

        if ($alreadyActive) {
            return response()->json([
                'error' => 'Vous avez déjà une séance en cours ("' . optional($alreadyActive->subject)->name . '"). Terminez-la avant d\'en démarrer une nouvelle.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($session) {
                // Mettre à jour le statut
                $session->update(['status' => 'active']);

                // Initialiser les présences pour tous les étudiants
                $students = $session->classe->students;
                foreach ($students as $student) {
                    Attendance::create([
                        'student_id' => $student->id,
                        'session_id' => $session->id,
                        'status' => 'absent',
                        'points_earned' => -1
                    ]);

                    // Décrémenter le score initial
                    $student->decrement('score', 1);
                }
            });

            broadcast(new SessionStarted($session->load(['classe', 'subject'])));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Erreur au démarrage de la séance #' . $session->id . ' : ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'error' => 'Impossible de démarrer la séance.',
                'debug' => config('app.debug') ? ($e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ')') : null,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Séance démarrée',
            'session' => $session->load(['classe', 'subject'])
        ]);
    }

    public function updateAttendance(Request $request, $sessionId)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:present,late,absent'
        ]);

        $session = Session::findOrFail($sessionId);
        $professor = Auth::user()->professor;
        
        if ($session->professor_id !== $professor->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $attendance = Attendance::where('session_id', $sessionId)
            ->where('student_id', $request->student_id)
            ->first();

        if (!$attendance) {
            return response()->json(['error' => 'Présence non trouvée'], 404);
        }

        // Mise à jour du statut et des points (valeurs signées : la note stockée
        // dans points_earned représente le delta réel déjà appliqué au score)
        $pointsMap = ['present' => 3, 'late' => 1, 'absent' => -1];
        $newPoints = $pointsMap[$request->status] ?? 0;

        DB::transaction(function () use ($attendance, $newPoints, $request) {
            $student = $attendance->student;

            // Annuler l'effet du delta précédent (score -= oldDelta)
            $oldPoints = $attendance->points_earned ?? 0;
            $student->decrement('score', $oldPoints);

            // Appliquer le nouveau delta (score += newDelta)
            $student->increment('score', $newPoints);
            
            // Mettre à jour les statistiques
            $this->updateStudentStats($student, $request->status, $attendance->status);
            
            // Mettre à jour l'attendance
            $attendance->update([
                'status' => $request->status,
                'points_earned' => $newPoints,
                'arrival_time' => now()
            ]);
        });

        $attendance->refresh()->load('student');

        // Stats fraîches de la séance, pour que le dashboard n'ait rien à recalculer lui-même
        $sessionAttendances = Attendance::where('session_id', $sessionId)->get();
        $stats = [
            'total' => $sessionAttendances->count(),
            'present' => $sessionAttendances->where('status', 'present')->count(),
            'late' => $sessionAttendances->where('status', 'late')->count(),
            'absent' => $sessionAttendances->where('status', 'absent')->count(),
        ];

        broadcast(new AttendanceMarked((int) $sessionId, [
            'student_id' => $attendance->student_id,
            'student_name' => trim(($attendance->student->first_name ?? '') . ' ' . ($attendance->student->last_name ?? '')),
            'status' => $attendance->status,
            'arrival_time' => optional($attendance->arrival_time)->format('H:i:s'),
            'points_earned' => $attendance->points_earned,
            'stats' => $stats,
        ]));

        return response()->json([
            'success' => true,
            'attendance' => $attendance
        ]);
    }

    private function updateStudentStats($student, $newStatus, $oldStatus)
    {
        // Réinitialiser les anciens compteurs
        if ($oldStatus === 'present') $student->decrement('total_presence');
        if ($oldStatus === 'late') $student->decrement('total_late');
        if ($oldStatus === 'absent') $student->decrement('total_absence');
        
        // Ajouter les nouveaux
        if ($newStatus === 'present') $student->increment('total_presence');
        if ($newStatus === 'late') $student->increment('total_late');
        if ($newStatus === 'absent') $student->increment('total_absence');
    }

    public function getSessionData($id)
    {
        $session = Session::with(['classe', 'subject', 'attendances.student'])
            ->findOrFail($id);
        
        $professor = Auth::user()->professor;
        if ($session->professor_id !== $professor->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $attendances = $session->attendances->map(function($att) {
            return [
                'id' => $att->id,
                'student_id' => $att->student_id,
                'name' => $att->student->full_name,
                'initials' => $att->student->initials,
                'class' => $att->student->classe->name,
                'status' => $att->status,
                'arrival_time' => $att->arrival_time ? $att->arrival_time->format('H:i:s') : null,
                'points' => $att->points_earned
            ];
        });

        return response()->json([
            'session' => $session,
            'attendances' => $attendances,
            'stats' => [
                'total' => $attendances->count(),
                'present' => $attendances->where('status', 'present')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'absent' => $attendances->where('status', 'absent')->count()
            ]
        ]);
    }

    public function endSession($id)
    {
        $session = Session::findOrFail($id);
        $professor = Auth::user()->professor;
        
        if ($session->professor_id !== $professor->id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $session->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Séance terminée'
        ]);
    }

    /**
     * Applique les filtres communs (date de/à) à une requête de séances
     * du professeur connecté. Utilisé à la fois par mySessions() et
     * exportSessionsCsv() pour garantir que l'export corresponde
     * toujours exactement à ce que le professeur voit à l'écran.
     */
    private function filteredSessionsQuery(Request $request, $professorId)
    {
        $query = Session::where('professor_id', $professorId)
            ->with(['subject', 'classe'])
            ->withCount([
                'attendances as total_attendances',
                'attendances as present_count' => function ($q) {
                    $q->whereIn('status', ['present', 'late']);
                }
            ]);

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        return $query->orderBy('date', 'desc')->orderBy('start_time', 'desc');
    }

    /**
     * Page "Mes séances" : historique complet des séances du professeur,
     * paginé (10 par page), filtrable par plage de dates.
     */
    public function mySessions(Request $request)
    {
        $professor = Auth::user()->professor;

        if (!$professor) {
            return redirect()->route('home')->with('error', 'Profil professeur introuvable.');
        }

        $sessions = $this->filteredSessionsQuery($request, $professor->id)
            ->paginate(10)
            ->withQueryString();

        return view('professor.sessions', compact('sessions'));
    }

    /**
     * Export CSV de l'historique des séances (respecte les mêmes filtres
     * de date que la page "Mes séances"), à destination de l'administration.
     */
    public function exportSessionsCsv(Request $request)
    {
        $professor = Auth::user()->professor;

        if (!$professor) {
            return redirect()->route('home')->with('error', 'Profil professeur introuvable.');
        }

        $sessions = $this->filteredSessionsQuery($request, $professor->id)->get();

        $filename = 'mes_seances_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->streamDownload(function () use ($sessions) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 pour un affichage correct des accents dans Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Matière',
                'Classe',
                'Date',
                'Campus',
                'Salle',
                'Heure de début',
                'Heure de fin',
                'Durée (min)',
                'Étudiants présents',
                'Total étudiants',
                'Taux de présence (%)',
                'Statut'
            ]);

            foreach ($sessions as $session) {
                $total = $session->total_attendances;
                $present = $session->present_count;
                $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

                fputcsv($handle, [
                    $session->subject->name ?? 'N/A',
                    $session->classe->name ?? 'N/A',
                    optional($session->date)->format('d/m/Y'),
                    $session->campus === 'ons' ? 'ONS' : ($session->campus === 'mharza' ? 'Mharza' : $session->campus),
                    $session->room,
                    optional($session->start_time)->format('H:i'),
                    optional($session->end_time)->format('H:i'),
                    $session->duration,
                    $present,
                    $total,
                    $rate,
                    ucfirst($session->status)
                ]);
            }

            fclose($handle);
        }, $filename, $headers);
    }

    /**
     * Page "Étudiants" : pour chaque séance ayant démarré, la liste
     * détaillée des étudiants présents / en retard / absents.
     */
    public function studentsBySession(Request $request)
    {
        $professor = Auth::user()->professor;

        if (!$professor) {
            return redirect()->route('home')->with('error', 'Profil professeur introuvable.');
        }

        $query = Session::where('professor_id', $professor->id)
            ->whereIn('status', ['active', 'completed'])
            ->with(['subject', 'classe', 'attendances.student']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $sessions = $query->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(5)
            ->withQueryString();

        // Regroupe les présences de chaque séance par statut, avec le nom complet de l'étudiant
        $sessions->getCollection()->transform(function ($session) {
            $grouped = ['present' => collect(), 'late' => collect(), 'absent' => collect()];

            foreach ($session->attendances as $attendance) {
                if (!$attendance->student) {
                    continue;
                }

                $entry = (object) [
                    'id' => $attendance->student->id,
                    'name' => trim(($attendance->student->first_name ?? '') . ' ' . ($attendance->student->last_name ?? '')),
                    'matricule' => $attendance->student->matricule,
                    'arrival_time' => $attendance->arrival_time,
                ];

                $status = in_array($attendance->status, ['present', 'late', 'absent']) ? $attendance->status : 'absent';
                $grouped[$status]->push($entry);
            }

            $session->present_list = $grouped['present']->sortBy('name')->values();
            $session->late_list = $grouped['late']->sortBy('name')->values();
            $session->absent_list = $grouped['absent']->sortBy('name')->values();
            $session->total_students = $session->attendances->count();

            return $session;
        });

        // Classes du professeur pour le filtre
        $professorClasses = Classe::whereIn('id', Session::where('professor_id', $professor->id)->pluck('class_id')->unique())
            ->orderBy('name')
            ->get();

        return view('professor.students', compact('sessions', 'professorClasses'));
    }
}