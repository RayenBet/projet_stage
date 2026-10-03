<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\CertificateController;

// Page d'accueil
Route::get('/', function () {
    return view('index');
})->name('home');

// Authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard
Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth');

// Routes Étudiant
Route::prefix('student')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/history', [StudentController::class, 'attendanceHistory'])->name('student.history');
    Route::get('/certificates', [StudentController::class, 'certificates'])->name('student.certificates');
    Route::get('/certificates/{id}', [StudentController::class, 'certificateShow'])->name('student.certificates.show');
    Route::post('/certificates/{id}/claim', [StudentController::class, 'claimCertificate'])->name('student.certificates.claim');
    Route::get('/ranking', [StudentController::class, 'ranking'])->name('student.ranking');
});

// Routes Professeur
Route::prefix('professor')->middleware(['auth', 'role:professor'])->group(function () {
    Route::get('/dashboard', [ProfessorController::class, 'dashboard'])->name('professor.dashboard');
    Route::post('/session', [ProfessorController::class, 'createSession'])->name('professor.session.create');
    Route::post('/session/{id}/start', [ProfessorController::class, 'startSession']);
    Route::post('/session/{id}/end', [ProfessorController::class, 'endSession']);
    Route::get('/session/{id}/data', [ProfessorController::class, 'getSessionData']);
    Route::post('/session/{sessionId}/attendance', [ProfessorController::class, 'updateAttendance']);
    Route::get('/sessions', [ProfessorController::class, 'mySessions'])->name('professor.sessions');
    Route::get('/sessions/export', [ProfessorController::class, 'exportSessionsCsv'])->name('professor.sessions.export');
    Route::get('/students', [ProfessorController::class, 'studentsBySession'])->name('professor.students');
});

// Routes Admin
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // Classes
    Route::resource('classes', ClassController::class);
    
    // Étudiants
    Route::resource('students', AdminStudentController::class);
    
    // Matières
    Route::resource('subjects', SubjectController::class);
    
    // Séances
    Route::resource('sessions', SessionController::class);
    
    // Certificats
    Route::resource('certificates', CertificateController::class);
});

// WebSocket (Laravel Echo)
Route::get('/socket', function () {
    return view('socket');
});