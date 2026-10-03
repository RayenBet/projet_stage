<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Palier visuel (Bronze/Argent/Or/Platine) selon le rang du certificat
     * INTERNE parmi tous les certificats internes. Purement esthétique.
     */
    private function tierFor(int $position, int $total): array
    {
        $ratio = $total > 1 ? $position / ($total - 1) : 0;

        return match (true) {
            $ratio <= 0.25 => ['label' => 'Bronze', 'color' => '#B45309', 'bg' => '#FEF3C7', 'icon' => 'fa-medal'],
            $ratio <= 0.5  => ['label' => 'Argent', 'color' => '#64748B', 'bg' => '#F1F5F9', 'icon' => 'fa-award'],
            $ratio <= 0.75 => ['label' => 'Or', 'color' => '#B7791F', 'bg' => '#FFFBEB', 'icon' => 'fa-trophy'],
            default        => ['label' => 'Platine', 'color' => '#4F46E5', 'bg' => '#EEF2FF', 'icon' => 'fa-crown'],
        };
    }

    /**
     * Badge visuel pour un certificat EXTERNE, basé sur ses propres colonnes.
     */
    private function badgeFor(Certificate $certificate): array
    {
        return [
            'label' => $certificate->category ?? 'Certification',
            'color' => $certificate->color ?? '#0EA5E9',
            'bg'    => '#F0F9FF',
            'icon'  => $certificate->icon ?? 'fa-certificate',
        ];
    }

    public function dashboard()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('home')->with('error', 'Profil étudiant introuvable.');
        }

        $history = Attendance::where('student_id', $student->id)
                    ->with(['session.subject', 'session.classe'])
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();

        $totalSessions = $history->count();
        $presentCount = $history->where('status', 'present')->count();
        $lateCount = $history->where('status', 'late')->count();

        $certificates = Certificate::orderBy('min_score', 'asc')->get();
        $unlockedCertificates = $student->certificates;
        $rank = Student::where('score', '>', $student->score)->count() + 1;

        return view('student.dashboard', compact(
            'totalSessions',
            'presentCount',
            'lateCount',
            'history',
            'certificates',
            'unlockedCertificates',
            'rank'
        ));
    }

    public function attendanceHistory(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('home')->with('error', 'Profil étudiant introuvable.');
        }

        $query = Attendance::where('student_id', $student->id)
                    ->with(['session.subject', 'session.classe']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereHas('session', fn ($q) => $q->whereDate('date', '>=', $request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->whereHas('session', fn ($q) => $q->whereDate('date', '<=', $request->date_to));
        }

        $history = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Statistiques globales (indépendantes des filtres/pagination) pour une vue d'ensemble fiable
        $allAttendances = Attendance::where('student_id', $student->id)->get();
        $globalTotal = $allAttendances->count();
        $globalPresent = $allAttendances->where('status', 'present')->count();
        $globalLate = $allAttendances->where('status', 'late')->count();
        $globalAbsent = $allAttendances->where('status', 'absent')->count();
        $globalRate = $globalTotal > 0 ? round((($globalPresent + $globalLate) / $globalTotal) * 100) : 0;

        return view('student.history', compact(
            'history',
            'student',
            'globalTotal',
            'globalPresent',
            'globalLate',
            'globalAbsent',
            'globalRate'
        ));
    }

    public function certificates()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('home')->with('error', 'Profil étudiant introuvable.');
        }

        $unlockedCertificates = $student->certificates;
        $unlockedIds = $unlockedCertificates->pluck('id');

        // --- Certificats internes ---
        $internalCertificates = Certificate::where('type', 'interne')
            ->orderBy('min_score', 'asc')
            ->get();

        $totalInternal = $internalCertificates->count();
        $internalCertificates = $internalCertificates->values()->map(function ($certificate, $index) use ($totalInternal) {
            $certificate->badge = $this->tierFor($index, $totalInternal);
            return $certificate;
        });

        // --- Certifications externes ---
        $externalCertificates = Certificate::where('type', 'externe')
            ->orderBy('min_score', 'asc')
            ->get()
            ->map(function ($certificate) {
                $certificate->badge = $this->badgeFor($certificate);
                return $certificate;
            });

        $allCertificates = $internalCertificates->concat($externalCertificates);

        // "Prochain objectif" = premier certificat NON OBTENU dont le seuil est le plus proche
        $nextCertificate = $allCertificates
            ->reject(fn ($c) => $unlockedIds->contains($c->id))
            ->sortBy('min_score')
            ->first();

        $totalCertificates = $allCertificates->count();
        $unlockedCount = $unlockedCertificates->count();

        return view('student.certificates', compact(
            'student',
            'internalCertificates',
            'externalCertificates',
            'unlockedIds',
            'totalCertificates',
            'unlockedCount',
            'nextCertificate'
        ));
    }

    public function certificateShow($id)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('home')->with('error', 'Profil étudiant introuvable.');
        }

        $certificate = Certificate::findOrFail($id);

        // Un certificat n'est "obtenu" QUE si l'étudiant l'a explicitement réclamé
        // (présence réelle dans la table pivot) — plus aucun déblocage automatique.
        $unlocked = $student->certificates->contains($certificate->id);
        $canClaim = !$unlocked && $student->score >= $certificate->min_score;

        $unlockedAt = $unlocked
            ? optional($student->certificates->firstWhere('id', $certificate->id)->pivot)->created_at
            : null;

        $holdersCount = $certificate->students()->count();

        $badge = $certificate->isInternal()
            ? (function () use ($certificate) {
                $siblings = Certificate::where('type', 'interne')->orderBy('min_score', 'asc')->get()->values();
                $position = $siblings->search(fn ($c) => $c->id === $certificate->id);
                return $this->tierFor($position, $siblings->count());
            })()
            : $this->badgeFor($certificate);

        $progress = min(100, ($student->score / max(1, $certificate->min_score)) * 100);
        $pointsRemaining = max(0, $certificate->min_score - $student->score);

        $allCertificates = Certificate::orderBy('min_score', 'asc')->get()->values();
        $position = $allCertificates->search(fn ($c) => $c->id === $certificate->id);
        $total = $allCertificates->count();
        $previousCertificate = $position > 0 ? $allCertificates[$position - 1] : null;
        $nextCertificateInList = $position < $total - 1 ? $allCertificates[$position + 1] : null;

        return view('student.certificate-show', compact(
            'student',
            'certificate',
            'badge',
            'unlocked',
            'canClaim',
            'progress',
            'pointsRemaining',
            'unlockedAt',
            'holdersCount',
            'previousCertificate',
            'nextCertificateInList'
        ));
    }

    /**
     * L'étudiant choisit volontairement d'obtenir un certificat.
     * Le coût en points (min_score) est alors déduit de son score.
     */
    public function claimCertificate($id)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('home')->with('error', 'Profil étudiant introuvable.');
        }

        $certificate = Certificate::findOrFail($id);

        if ($student->certificates->contains($certificate->id)) {
            return back()->with('info', 'Vous avez déjà obtenu ce certificat.');
        }

        if ($student->score < $certificate->min_score) {
            return back()->with('error', 'Vous n\'avez pas assez de points pour réclamer ce certificat.');
        }

        DB::transaction(function () use ($student, $certificate) {
            $student->certificates()->attach($certificate->id);
            $student->decrement('score', $certificate->min_score);
        });

        return redirect()
            ->route('student.certificates.show', $certificate->id)
            ->with('success', 'Félicitations ! Vous venez d\'obtenir "' . $certificate->name . '".');
    }

    /**
     * Classement des étudiants de la même classe, avec bonifications
     * pour le podium (réduction sur les frais d'éducation).
     */
    public function ranking()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('home')->with('error', 'Profil étudiant introuvable.');
        }

        $classmates = Student::where('class_id', $student->class_id)
            ->orderBy('score', 'desc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->values();

        $myPosition = $classmates->search(fn ($s) => $s->id === $student->id);
        $myRank = $myPosition !== false ? $myPosition + 1 : null;

        $podium = $classmates->take(3);
        $others = $classmates->slice(3)->values();

        $discounts = [1 => 7, 2 => 5, 3 => 2];

        return view('student.ranking', compact(
            'student',
            'classmates',
            'myRank',
            'podium',
            'others',
            'discounts'
        ));
    }
}