<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration - IIT Attendance')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        .sidebar-admin {
            background: linear-gradient(180deg, #0F172A 0%, #1E293B 100%);
            width: 280px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 50;
            padding: 24px 16px;
            overflow-y: auto;
        }
        
        .sidebar-admin .nav-item {
            padding: 12px 16px;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
        }
        
        .sidebar-admin .nav-item:hover {
            background: rgba(255,255,255,0.08);
            color: white;
        }
        
        .sidebar-admin .nav-item.active {
            background: rgba(99,102,241,0.2);
            color: #818CF8;
        }
        
        .main-content-admin {
            margin-left: 280px;
            min-height: 100vh;
            background: #F8FAFC;
        }
        
        .card-admin {
            background: white;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #F1F5F9;
            transition: all 0.3s ease;
        }
        
        .card-admin:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,0.06);
        }
        
        .btn-admin {
            padding: 8px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }
        
        .btn-admin-primary {
            background: #4F46E5;
            color: white;
        }
        
        .btn-admin-primary:hover {
            background: #4338CA;
            transform: translateY(-1px);
        }
        
        .btn-admin-danger {
            background: #EF4444;
            color: white;
        }
        
        .btn-admin-danger:hover {
            background: #DC2626;
        }
        
        .btn-admin-success {
            background: #22C55E;
            color: white;
        }
        
        .btn-admin-success:hover {
            background: #16A34A;
        }
        
        @media (max-width: 1024px) {
            .sidebar-admin { width: 80px; padding: 16px 8px; }
            .sidebar-admin .nav-item span { display: none; }
            .main-content-admin { margin-left: 80px; }
            .sidebar-admin .logo-text { display: none; }
        }
    </style>
</head>
<body>
    <!-- Sidebar Admin -->
    <aside class="sidebar-admin">
        <div class="flex items-center gap-3 mb-8 px-2">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                <i class="fas fa-robot text-white text-lg"></i>
            </div>
            <span class="text-white font-extrabold text-lg logo-text">IIT Admin</span>
        </div>
        
        <nav class="space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="nav-item @yield('active_dashboard', '')">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.classes.index') }}" class="nav-item @yield('active_classes', '')">
                <i class="fas fa-building"></i>
                <span>Classes</span>
            </a>
            <a href="{{ route('admin.students.index') }}" class="nav-item @yield('active_students', '')">
                <i class="fas fa-user-graduate"></i>
                <span>Étudiants</span>
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="nav-item @yield('active_subjects', '')">
                <i class="fas fa-book"></i>
                <span>Matières</span>
            </a>
            <a href="{{ route('admin.sessions.index') }}" class="nav-item @yield('active_sessions', '')">
                <i class="fas fa-calendar-alt"></i>
                <span>Séances</span>
            </a>
            <a href="{{ route('admin.certificates.index') }}" class="nav-item @yield('active_certificates', '')">
                <i class="fas fa-certificate"></i>
                <span>Certificats</span>
            </a>
            <div class="border-t border-white/10 my-4"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item w-full text-red-400 hover:bg-red-500/10">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </button>
            </form>
        </nav>
        
        <div class="absolute bottom-6 left-4 right-4">
            <div class="bg-white/5 rounded-2xl p-4 text-center">
                <p class="text-white text-sm font-semibold">👤 {{ Auth::user()->name ?? 'Admin' }}</p>
                <p class="text-gray-400 text-xs">Administrateur</p>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content-admin">
        <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-40">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900">@yield('page_title', 'Administration')</h2>
                    <p class="text-sm text-gray-500">@yield('page_subtitle', 'Gestion complète du système')</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500 hidden md:block">📅 {{ now()->locale('fr')->isoFormat('dddd, D MMMM YYYY') }}</span>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-sm font-medium text-gray-700">Système en ligne</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="p-8">
            @yield('content')
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @yield('scripts')
</body>
</html> 