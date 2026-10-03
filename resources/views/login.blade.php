@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 flex items-center justify-center px-6">
    <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-3xl p-8 max-w-md w-full">
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo.svg') }}" alt="IIT Attendance" class="w-16 h-16 rounded-2xl mx-auto shadow-lg shadow-indigo-500/30 mb-4">
            <h2 class="text-2xl font-bold text-white">Connexion</h2>
            <p class="text-gray-400 text-sm mt-1">Connectez-vous à votre espace personnel</p>
            <div class="mt-4">
                <span class="inline-block px-4 py-2 bg-indigo-500/20 text-indigo-300 rounded-full text-sm font-semibold border border-indigo-500/20">
                    @if($role === 'professor')
                        <i class="fas fa-chalkboard-teacher mr-2"></i> Professeur
                    @else
                        <i class="fas fa-user-graduate mr-2"></i> Étudiant
                    @endif
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="role" value="{{ $role }}">
            
            <div class="mb-4">
                <label class="text-gray-300 text-sm font-medium block mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                       class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition">
                @error('email')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="text-gray-300 text-sm font-medium block mb-2">Mot de passe</label>
                <input type="password" name="password" 
                       class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition">
                @error('password')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition shadow-lg shadow-indigo-500/30">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Se connecter
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-sm transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>
@endsection