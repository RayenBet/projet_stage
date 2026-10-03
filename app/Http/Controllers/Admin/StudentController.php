<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Student;
use App\Models\Classe;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $students = Student::with(['user', 'classe'])->get();
        $classes = Classe::all();
        return view('admin.students', compact('students', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'matricule' => 'required|string|unique:students',
            'class_id' => 'required|exists:classes,id'
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'role' => 'student'
        ]);

        // Créer l'étudiant
        $student = Student::create([
            'user_id' => $user->id,
            'class_id' => $request->class_id,
            'matricule' => $request->matricule,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'score' => 0,
            'total_presence' => 0,
            'total_absence' => 0,
            'total_late' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Étudiant créé avec succès',
            'student' => $student->load(['user', 'classe'])
        ]);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'matricule' => 'required|string|unique:students,matricule,' . $id,
            'class_id' => 'required|exists:classes,id',
            'score' => 'nullable|integer'
        ]);

        // Mettre à jour l'utilisateur
        $student->user->update([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email
        ]);

        // Mettre à jour l'étudiant
        $student->update([
            'class_id' => $request->class_id,
            'matricule' => $request->matricule,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'score' => $request->score ?? $student->score
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Étudiant mis à jour',
            'student' => $student->load(['user', 'classe'])
        ]);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $user = $student->user;
        
        $student->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Étudiant supprimé'
        ]);
    }
}