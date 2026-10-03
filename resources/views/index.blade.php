@extends('layouts.app')

@section('title', 'Accueil - IIT Attendance System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900">
    <nav class="fixed top-0 w-full z-50 bg-white/95 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo.svg') }}" alt="IIT Attendance" class="w-12 h-12 rounded-2xl shadow-lg shadow-indigo-500/30">
                <div>
                    <h1 class="font-extrabold text-xl text-gray-900 tracking-tight">IIT <span class="text-indigo-600">Attendance</span></h1>
                    <p class="text-xs text-gray-500 font-medium">Système Intelligent de Présence</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-400 hidden md:block">Université Internationale de Technologie</span>
            </div>
        </div>
    </nav>

    <div class="min-h-screen flex items-center justify-center pt-20 px-6">
        <div class="max-w-6xl w-full">
            <div class="text-center mb-16">

                <h1 class="text-5xl font-extrabold text-white leading-tight">
                    Gérez vos présences
                    <span class="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">intelligemment</span>
                </h1>
                <p class="text-xl text-gray-300 mt-4">Choisissez votre rôle pour accéder à votre espace personnel</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Étudiant -->
                <a href="{{ route('login', ['role' => 'student']) }}" 
                   class="group bg-white/5 backdrop-blur-lg border border-white/10 rounded-3xl p-12 text-center hover:bg-white/10 transition-all hover:scale-[1.02] hover:border-indigo-500/50">
                    <div class="w-24 h-24 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-3xl flex items-center justify-center mx-auto text-4xl shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-graduate text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mt-6">Étudiant</h3>
                    <p class="text-gray-400 mt-3">Suivez vos présences, points et certificats</p>
                    <div class="mt-6 text-indigo-400 group-hover:translate-x-2 transition-transform">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <!-- Professeur -->
                <a href="{{ route('login', ['role' => 'professor']) }}" 
                   class="group bg-white/5 backdrop-blur-lg border border-white/10 rounded-3xl p-12 text-center hover:bg-white/10 transition-all hover:scale-[1.02] hover:border-emerald-500/50">
                    <div class="w-24 h-24 bg-gradient-to-br from-emerald-600 to-green-600 rounded-3xl flex items-center justify-center mx-auto text-4xl shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                        <i class="fas fa-chalkboard-teacher text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mt-6">Professeur</h3>
                    <p class="text-gray-400 mt-3">Gérez vos séances et suivez vos étudiants</p>
                    <div class="mt-6 text-emerald-400 group-hover:translate-x-2 transition-transform">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection