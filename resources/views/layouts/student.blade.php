<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Étudiant - IIT Attendance')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    
    <!-- Styles CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            align-items: flex-start;
            min-height: 100vh;
        }

        .sidebar-student {
            background: linear-gradient(180deg, #0F172A 0%, #1E3A5F 100%);
            width: 280px;
            flex: 0 0 280px;
            min-height: 100vh;
            height: 100vh;
            position: sticky;
            top: 0;
            z-index: 50;
            padding: 24px 16px;
            overflow-y: auto;
        }
        
        .sidebar-student .nav-item {
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
        
        .sidebar-student .nav-item:hover {
            background: rgba(255,255,255,0.08);
            color: white;
        }
        
        .sidebar-student .nav-item.active {
            background: rgba(99,102,241,0.2);
            color: #818CF8;
        }
        
        .main-content-student {
            flex: 1 1 auto;
            min-width: 0;
            min-height: 100vh;
            background: #F8FAFC;
        }
        
        .score-circle {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: conic-gradient(#4F46E5 0deg, #4F46E5 0deg, #E2E8F0 0deg, #E2E8F0 360deg);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .score-circle-inner {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .score-circle-inner .score-number {
            font-size: 36px;
            font-weight: 800;
            color: #0F172A;
        }
        
        .certificate-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #F1F5F9;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .certificate-card.locked {
            opacity: 0.6;
        }
        
        .certificate-card.locked::after {
            content: '🔒';
            position: absolute;
            top: 12px;
            right: 16px;
            font-size: 20px;
        }
        
        .certificate-card.unlocked {
            border-color: #4F46E5;
            box-shadow: 0 4px 20px rgba(79,70,229,0.15);
        }
        
        .certificate-card.unlocked::after {
            content: '✅';
            position: absolute;
            top: 12px;
            right: 16px;
            font-size: 20px;
        }
        
        .certificate-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        }
        
        .progress-bar-cert {
            height: 6px;
            background: #E2E8F0;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 8px;
        }
        
        .progress-bar-cert .fill {
            height: 100%;
            border-radius: 3px;
            transition: width 1s ease;
        }
        
        .history-timeline {
            position: relative;
            padding-left: 28px;
        }
        
        .history-timeline::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #E2E8F0;
        }
        
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 6px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #4F46E5;
            background: white;
        }
        
        .timeline-item.present::before {
            border-color: #22C55E;
            background: #22C55E;
        }
        
        .timeline-item.late::before {
            border-color: #F59E0B;
            background: #F59E0B;
        }
        
        .timeline-item.absent::before {
            border-color: #EF4444;
            background: #EF4444;
        }
        
        .badge-rank {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
        }
        
        .badge-rank.gold { background: #FEF3C7; color: #B45309; }
        .badge-rank.silver { background: #F1F5F9; color: #64748B; }
        .badge-rank.bronze { background: #FED7AA; color: #9A3412; }
        
        @media (max-width: 1024px) {
            .sidebar-student { width: 80px; flex-basis: 80px; padding: 16px 8px; }
            .sidebar-student .nav-item span { display: none; }
            .sidebar-student .logo-text { display: none; }
        }
    </style>
</head>
<body>
    <!-- Sidebar Étudiant -->
    <aside class="sidebar-student">
        <div class="flex items-center gap-3 mb-8 px-2">
            <img src="{{ asset('images/logo.svg') }}" alt="IIT Attendance" class="w-10 h-10 rounded-xl shadow-lg shadow-indigo-500/30 flex-shrink-0">
            <span class="text-white font-extrabold text-lg logo-text">IIT Etudiant</span>
        </div>
        
        <nav class="space-y-1">
            <a href="{{ route('student.dashboard') }}" class="nav-item @yield('active_dashboard', '')">
                <i class="fas fa-home"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="{{ route('student.history') }}" class="nav-item @yield('active_history', '')">
                <i class="fas fa-clock"></i>
                <span>Mes présences</span>
            </a>
            <a href="{{ route('student.certificates') }}" class="nav-item @yield('active_certificates', '')">
                <i class="fas fa-certificate"></i>
                <span>Mes certificats</span>
            </a>
            <a href="{{ route('student.ranking') }}" class="nav-item @yield('active_ranking', '')">
                <i class="fas fa-trophy"></i>
                <span>Classement</span>
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
                <p class="text-white text-sm font-semibold">👨‍🎓 {{ Auth::user()->student->first_name ?? 'Étudiant' }} {{ Auth::user()->student->last_name ?? '' }}</p>
                <p class="text-gray-400 text-xs">{{ Auth::user()->student->classe->name ?? 'Classe' }} • Matricule: {{ Auth::user()->student->matricule ?? 'N/A' }}</p>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content-student">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-40">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900">@yield('page_title', 'Tableau de bord')</h2>
                    <p class="text-sm text-gray-500">@yield('page_subtitle', 'Bienvenue sur votre espace étudiant')</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500 hidden md:block">📅 {{ now()->locale('fr')->isoFormat('dddd, D MMMM YYYY') }}</span>
                    <div class="flex items-center gap-2 bg-indigo-50 px-4 py-2 rounded-full">
                        <i class="fas fa-star text-indigo-600"></i>
                        <span class="text-sm font-bold text-indigo-600"><span data-counter="{{ Auth::user()->student->score ?? 0 }}">0</span> pts</span>
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
    @include('partials.confetti')
</body>
</html>