<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $role = $request->query('role', 'student');
        return view('login', compact('role'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:student,professor,admin'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Les identifiants sont incorrects.',
            ]);
        }

        if ($user->role !== $request->role) {
            return back()->withErrors([
                'role' => 'Rôle incorrect pour ce compte.'
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->action([AuthController::class, 'dashboard']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function dashboard()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        if ($user->role === 'student') {
            if (!\Route::has('student.dashboard')) {
                return "ERREUR : la route 'student.dashboard' n'existe pas.";
            }
            return redirect()->route('student.dashboard');
        } elseif ($user->role === 'professor') {
            if (!\Route::has('professor.dashboard')) {
                return "ERREUR : la route 'professor.dashboard' n'existe pas.";
            }
            return redirect()->route('professor.dashboard');
        } elseif ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect('/');
    }
}