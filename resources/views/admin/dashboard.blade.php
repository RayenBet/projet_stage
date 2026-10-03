@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page_title', '📊 Tableau de bord Administrateur')
@section('page_subtitle', 'Vue d\'ensemble du système')
@section('active_dashboard', 'active')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="card-admin">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 text-xl">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Student::count() }}</p>
                <p class="text-sm text-gray-500">Étudiants</p>
            </div>
        </div>
    </div>
    <div class="card-admin">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 text-xl">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Professor::count() }}</p>
                <p class="text-sm text-gray-500">Professeurs</p>
            </div>
        </div>
    </div>
    <div class="card-admin">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 text-xl">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Classe::count() }}</p>
                <p class="text-sm text-gray-500">Classes</p>
            </div>
        </div>
    </div>
    <div class="card-admin">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 text-xl">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Session::where('status', 'active')->count() }}</p>
                <p class="text-sm text-gray-500">Séances actives</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card-admin">
        <h3 class="font-bold text-gray-900 mb-4">📈 Statistiques globales</h3>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">Présence moyenne</span>
                    <span class="font-bold text-indigo-600">87%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full" style="width: 87%;"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">Taux de réussite</span>
                    <span class="font-bold text-emerald-600">92%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-emerald-600 h-2 rounded-full" style="width: 92%;"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">Certificats délivrés</span>
                    <span class="font-bold text-purple-600">{{ \App\Models\StudentCertificate::count() }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: 65%;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-admin">
        <h3 class="font-bold text-gray-900 mb-4">📋 Actions rapides</h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('admin.classes.index') }}" class="p-4 bg-indigo-50 rounded-xl text-center hover:bg-indigo-100 transition">
                <i class="fas fa-building text-2xl text-indigo-600"></i>
                <p class="text-sm font-medium text-gray-700 mt-2">Gérer les classes</p>
            </a>
            <a href="{{ route('admin.students.index') }}" class="p-4 bg-emerald-50 rounded-xl text-center hover:bg-emerald-100 transition">
                <i class="fas fa-user-graduate text-2xl text-emerald-600"></i>
                <p class="text-sm font-medium text-gray-700 mt-2">Gérer les étudiants</p>
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="p-4 bg-blue-50 rounded-xl text-center hover:bg-blue-100 transition">
                <i class="fas fa-book text-2xl text-blue-600"></i>
                <p class="text-sm font-medium text-gray-700 mt-2">Gérer les matières</p>
            </a>
            <a href="{{ route('admin.certificates.index') }}" class="p-4 bg-purple-50 rounded-xl text-center hover:bg-purple-100 transition">
                <i class="fas fa-certificate text-2xl text-purple-600"></i>
                <p class="text-sm font-medium text-gray-700 mt-2">Gérer les certificats</p>
            </a>
        </div>
    </div>
</div>
@endsection