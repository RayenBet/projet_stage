<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Professeur - IIT Attendance')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    
    <!-- Styles CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        .sidebar-prof {
            background: linear-gradient(180deg, #0F172A 0%, #064E3B 100%);
            width: 280px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 50;
            padding: 24px 16px;
            overflow-y: auto;
        }
        
        .sidebar-prof .nav-item {
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
        
        .sidebar-prof .nav-item:hover {
            background: rgba(255,255,255,0.08);
            color: white;
        }
        
        .sidebar-prof .nav-item.active {
            background: rgba(16,185,129,0.2);
            color: #34D399;
        }
        
        .sidebar-prof .nav-item i {
            width: 22px;
            text-align: center;
            font-size: 18px;
        }
        
        .main-content-prof {
            margin-left: 280px;
            min-height: 100vh;
            background: #F8FAFC;
        }
        
        .card-prof {
            background: white;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #F1F5F9;
            transition: all 0.3s ease;
        }
        
        .card-prof:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,0.06);
        }
        
        .status-badge {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .status-present { background: #DCFCE7; color: #15803D; }
        .status-late { background: #FEF3C7; color: #B45309; }
        .status-absent { background: #FEE2E2; color: #B91C1C; }
        .status-cancelled { background: #F3F4F6; color: #6B7280; }
        
        .form-select-prof {
            background: white;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 12px 16px;
            width: 100%;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 20px;
        }
        
        .form-select-prof:focus {
            outline: none;
            border-color: #10B981;
            box-shadow: 0 0 0 4px rgba(16,185,129,0.1);
        }
        
        .btn-start-session {
            background: linear-gradient(135deg, #059669, #10B981);
            color: white;
            padding: 14px 32px;
            border: none;
            border-radius: 16px;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-start-session:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(5,150,105,0.3);
        }
        
        .campus-selector {
            display: flex;
            gap: 12px;
            padding: 4px;
            background: #F1F5F9;
            border-radius: 16px;
        }
        
        .campus-btn {
            padding: 8px 20px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: transparent;
            color: #64748B;
        }
        
        .campus-btn.active {
            background: white;
            color: #0F172A;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .campus-btn:hover:not(.active) {
            background: rgba(255,255,255,0.5);
        }
        
        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .live-indicator.active {
            background: #DCFCE7;
            color: #15803D;
        }
        
        .live-indicator.inactive {
            background: #FEE2E2;
            color: #B91C1C;
        }
        
        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .live-dot.active {
            background: #22C55E;
            animation: pulse-dot 1.5s ease-in-out infinite;
        }
        
        .live-dot.inactive {
            background: #EF4444;
        }
        
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }
        
        .table-attendance {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table-attendance th {
            text-align: left;
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 600;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #F1F5F9;
        }
        
        .table-attendance td {
            padding: 12px 16px;
            font-size: 14px;
            border-bottom: 1px solid #F8FAFC;
        }
        
        .table-attendance tr:hover td {
            background: #F8FAFC;
        }
        
        .student-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            background: #EEF2FF;
            color: #4F46E5;
        }
        
        .history-item {
            padding: 12px 16px;
            border-left: 3px solid #E2E8F0;
            transition: all 0.3s ease;
        }
        
        .history-item:hover {
            border-left-color: #10B981;
            background: #F8FAFC;
        }
        
        @media (max-width: 1024px) {
            .sidebar-prof { width: 80px; padding: 16px 8px; }
            .sidebar-prof .nav-item span { display: none; }
            .main-content-prof { margin-left: 80px; }
            .sidebar-prof .logo-text { display: none; }
        }
    </style>
</head>
<body>
    <!-- Sidebar Professeur -->
    <aside class="sidebar-prof">
        <div class="flex items-center gap-3 mb-8 px-2">
            <img src="{{ asset('images/logo-emerald.svg') }}" alt="IIT Attendance" class="w-10 h-10 rounded-xl shadow-lg shadow-emerald-500/30 flex-shrink-0">
            <span class="text-white font-extrabold text-lg logo-text">IIT Professeur</span>
        </div>
        
        <nav class="space-y-1">
            <a href="{{ route('professor.dashboard') }}" class="nav-item @yield('active_dashboard', '')">
                <i class="fas fa-th-large"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="{{ route('professor.sessions') }}" class="nav-item @yield('active_sessions', '')">
                <i class="fas fa-history"></i>
                <span>Historique des séances</span>
            </a>
            <a href="{{ route('professor.students') }}" class="nav-item @yield('active_students', '')">
                <i class="fas fa-users"></i>
                <span>Étudiants</span>
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
                <p class="text-white text-sm font-semibold">👨‍🏫 {{ Auth::user()->professor->first_name ?? 'Professeur' }} {{ Auth::user()->professor->last_name ?? '' }}</p>
                <p class="text-gray-400 text-xs">{{ Auth::user()->professor->specialty ?? 'Enseignant' }}</p>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content-prof">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-40">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900">@yield('page_title', 'Tableau de bord')</h2>
                    <p class="text-sm text-gray-500">@yield('page_subtitle', 'Gérez vos séances et suivez les présences')</p>
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

    <!-- Scripts -->
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @yield('scripts')

    @include('partials.toast')
</body>
</html>