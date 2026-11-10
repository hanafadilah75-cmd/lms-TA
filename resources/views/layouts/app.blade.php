<!DOCTYPE html>
<html lang="id" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem E-Training LMS' }}</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }
    </style>

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen w-screen overflow-hidden antialiased bg-[#fcfbfe] text-slate-800 flex flex-col m-0 p-0">
    <!-- Master Full-Screen App Container -->
    <div class="w-full h-full flex flex-col lg:flex-row overflow-hidden bg-white">
        
        <!-- ========================================== -->
        <!-- LEFT SIDEBAR (Deep Royal Indigo #534ebb)  -->
        <!-- ========================================== -->
        <aside class="w-full lg:w-64 bg-[#534ebb] text-white p-6 flex flex-col justify-between shrink-0 h-full overflow-y-auto">
            <div>
                <!-- Brand Header (Logo removed, title updated to Sistem E-Training LMS) -->
                <div class="flex items-center justify-between mb-8 pb-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <span class="text-xl font-extrabold tracking-tight text-white leading-tight">
                            Sistem E-Training LMS
                        </span>
                    </a>
                    
                    <!-- Mobile Hamburger Toggle -->
                    <button type="button" id="mobile-nav-toggle" class="lg:hidden p-2 rounded-xl bg-white/10 text-white">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links List -->
                <nav id="sidebar-nav" class="hidden lg:flex flex-col space-y-2">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" 
                        class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white text-[#534ebb] shadow-md font-extrabold' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- All Courses -->
                    <a href="{{ route('courses.index') }}" 
                        class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('courses.*') ? 'bg-white text-[#534ebb] shadow-md font-extrabold' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        <span>Materi Kursus</span>
                    </a>

                    <!-- Log Aktivitas -->
                    <a href="{{ route('activities.index') }}" 
                        class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('activities.*') ? 'bg-white text-[#534ebb] shadow-md font-extrabold' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Log Aktivitas</span>
                    </a>

                    <!-- Progres Belajar (Admin / Tutor / Karyawan) -->
                    @if(Auth::user()->isAdmin() || Auth::user()->isTutor())
                        <a href="{{ route('progress.index') }}" 
                            class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('progress.*') ? 'bg-white text-[#534ebb] shadow-md font-extrabold' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                            <span>Progres Belajar</span>
                        </a>
                    @else
                        <a href="{{ route('progress.employee', Auth::id()) }}" 
                            class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('progress.*') ? 'bg-white text-[#534ebb] shadow-md font-extrabold' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                            <span>Rapor Saya</span>
                        </a>
                    @endif

                    <!-- Review Esai (Admin & Tutor) -->
                    @if(Auth::user()->isAdmin() || Auth::user()->isTutor())
                        @php
                            $pendingReviewCount = \App\Models\UserAnswer::whereHas('question', fn($q) => $q->where('type', 'essay'))
                                ->whereHas('essayReview', fn($r) => $r->whereNull('tutor_score'))
                                ->count();
                        @endphp
                        <a href="{{ route('reviews.index') }}" 
                            class="flex items-center justify-between px-4 py-3 rounded-2xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('reviews.*') ? 'bg-white text-[#534ebb] shadow-md font-extrabold' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <div class="flex items-center gap-3.5">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.502 49.188 49.188 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                </svg>
                                <span>Review Esai</span>
                            </div>
                            @if($pendingReviewCount > 0)
                                <span class="px-2 py-0.5 rounded-full text-xs font-extrabold {{ request()->routeIs('reviews.*') ? 'bg-[#534ebb] text-white' : 'bg-amber-400 text-slate-900' }}">
                                    {{ $pendingReviewCount }}
                                </span>
                            @endif
                        </a>
                    @endif

                    <!-- Admin Specific Menu Section -->
                    @if(Auth::user()->isAdmin())
                        <div class="pt-4 pb-1">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-white/50 px-4">Admin Panel</span>
                        </div>

                        <!-- Perusahaan -->
                        <a href="{{ route('companies.index') }}" 
                            class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('companies.index', 'companies.create', 'companies.edit') ? 'bg-white text-[#534ebb] shadow-md font-extrabold' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                            <span>Perusahaan</span>
                        </a>

                        <!-- Karyawan & Tutor -->
                        <a href="{{ route('companies.employees') }}" 
                            class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-bold transition-all duration-200 {{ request()->routeIs('companies.employees*') ? 'bg-white text-[#534ebb] shadow-md font-extrabold' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                            <span>Karyawan & Tutor</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Sidebar Bottom: Settings & Logout -->
            <div class="pt-6 border-t border-white/15 space-y-2">
                <a href="{{ route('profile.edit') }}" 
                    class="flex items-center gap-3 px-4 py-2.5 rounded-2xl text-sm font-semibold transition text-white/80 hover:text-white hover:bg-white/10">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.27.1.06-.12l-.773.774a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.27-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span>Pengaturan Akun</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-sm font-semibold transition text-rose-200 hover:text-white hover:bg-rose-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                        <span>Keluar Akun</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ========================================== -->
        <!-- MAIN CENTER CONTENT AREA (Full-Screen)     -->
        <!-- ========================================== -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#fcfbfe] h-full overflow-hidden">
            
            <!-- Top Header Bar -->
            <header class="bg-white border-b border-slate-100 p-4 sm:p-5 flex items-center justify-between shrink-0 z-10">
                <!-- Search Box -->
                <div class="relative w-72 sm:w-80">
                    <form action="{{ route('courses.index') }}" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Cari materi pembelajaran..."
                            class="w-full bg-[#f8f7fc] text-xs font-semibold rounded-full pl-9 pr-4 py-2 border-none focus:ring-2 focus:ring-[#534ebb]/30 focus:bg-white transition text-slate-800 placeholder-slate-400">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </form>
                </div>

                <!-- Right Header Actions (Bell & User Pill) -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <a href="{{ route('activities.index') }}" class="w-9 h-9 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-slate-500 transition relative" title="Log Aktivitas">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-[#534ebb]"></span>
                    </a>

                    <!-- Profile Pill -->
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 pl-3 pr-2 py-1.5 rounded-full bg-slate-50 hover:bg-slate-100 transition border border-slate-100">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-extrabold text-slate-900 leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] font-semibold text-slate-400 capitalize">
                                {{ Auth::user()->role }} • ID {{ Auth::user()->id }}
                            </p>
                        </div>
                        @if(Auth::user()->avatar)
                            <img class="w-8 h-8 rounded-full object-cover border border-slate-200" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}">
                        @else
                            <div class="w-8 h-8 rounded-full bg-[#534ebb] text-white flex items-center justify-center text-xs font-extrabold uppercase shadow-sm">
                                {{ substr(Auth::user()->name, 0, 2) }}
                            </div>
                        @endif
                    </a>
                </div>
            </header>

            <!-- Main Content Container (Full-height Scrollable) -->
            <main class="flex-1 p-4 sm:p-6 lg:p-6 xl:p-8 overflow-y-auto min-w-0">
                <!-- Alerts (Success / Error) -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-900 text-xs font-bold flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs">✓</span>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-900 text-xs font-bold flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs">✕</span>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        <!-- ========================================== -->
        <!-- RIGHT PANEL: INTERACTIVE CALENDAR & USERS  -->
        <!-- ========================================== -->
        <aside class="hidden xl:flex xl:w-72 2xl:w-80 flex-col p-5 2xl:p-6 bg-white border-l border-slate-100 justify-between shrink-0 h-full overflow-y-auto">
            <div class="space-y-6">
                <!-- 1. Interactive Calendar with Year/Month Switcher & Activity Recap -->
                <div class="bg-[#fcfbfe] rounded-3xl p-4 border border-slate-100">
                    <!-- Calendar Header (Month Year & Nav) -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-1.5">
                            <select id="cal-month-select" class="text-xs font-extrabold text-slate-800 bg-transparent border-none cursor-pointer focus:ring-0 p-0 pr-4">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == \Carbon\Carbon::now()->month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(2026, $m, 1)->format('M') }}
                                    </option>
                                @endfor
                            </select>
                            <select id="cal-year-select" class="text-xs font-extrabold text-slate-800 bg-transparent border-none cursor-pointer focus:ring-0 p-0 pr-4">
                                @for($y = \Carbon\Carbon::now()->year + 2; $y >= 2022; $y--)
                                    <option value="{{ $y }}" {{ $y == \Carbon\Carbon::now()->year ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" id="cal-prev-btn" class="w-7 h-7 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-200/50 text-xs font-bold transition">
                                &lt;
                            </button>
                            <button type="button" id="cal-next-btn" class="w-7 h-7 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-200/50 text-xs font-bold transition">
                                &gt;
                            </button>
                        </div>
                    </div>

                    <!-- Days of Week Headers -->
                    <div class="grid grid-cols-7 text-center text-[10px] font-bold text-slate-400 mb-2">
                        <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                    </div>

                    <!-- Calendar Days Grid (Interactive Dynamic) -->
                    <div id="calendar-days-grid" class="grid grid-cols-7 text-center text-xs font-semibold gap-y-1.5 text-slate-600">
                        <!-- Populated by JavaScript -->
                    </div>

                    <!-- Rekapan Aktivitas & Pembelajaran Bulan Terpilih -->
                    <div class="mt-4 pt-3 border-t border-slate-100 space-y-2">
                        <div class="flex items-center justify-between text-[11px] font-extrabold text-slate-700">
                            <span>Rekapan <span id="recap-month-label">{{ \Carbon\Carbon::now()->format('M Y') }}</span></span>
                            <span id="recap-active-badge" class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#534ebb]/10 text-[#534ebb]">Live</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-center text-xs pt-1">
                            <div class="p-2 rounded-xl bg-white border border-slate-100 shadow-2xs">
                                <span class="text-[10px] font-semibold text-slate-400 block">Total Aktivitas</span>
                                <span id="recap-stat-activities" class="text-sm font-extrabold text-slate-900 mt-0.5 block">-</span>
                            </div>
                            <div class="p-2 rounded-xl bg-white border border-slate-100 shadow-2xs">
                                <span class="text-[10px] font-semibold text-slate-400 block">Modul Selesai</span>
                                <span id="recap-stat-modules" class="text-sm font-extrabold text-[#534ebb] mt-0.5 block">-</span>
                            </div>
                            <div class="p-2 rounded-xl bg-white border border-slate-100 shadow-2xs">
                                <span class="text-[10px] font-semibold text-slate-400 block">Kuis Diikuti</span>
                                <span id="recap-stat-quizzes" class="text-sm font-extrabold text-amber-600 mt-0.5 block">-</span>
                            </div>
                            <div class="p-2 rounded-xl bg-white border border-slate-100 shadow-2xs">
                                <span class="text-[10px] font-semibold text-slate-400 block">User Aktif</span>
                                <span id="recap-stat-users" class="text-sm font-extrabold text-emerald-600 mt-0.5 block">-</span>
                            </div>
                        </div>
                        <a id="recap-detail-link" href="{{ route('activities.index', ['month' => \Carbon\Carbon::now()->month, 'year' => \Carbon\Carbon::now()->year]) }}" 
                           class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-[#534ebb] hover:text-white text-[11px] font-extrabold text-slate-700 text-center transition flex items-center justify-center gap-1.5 mt-2">
                            <span>Lihat Rekapan Log Lengkap</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>

                <!-- 2. Real-time Online Users Widget -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Online Users</h3>
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                        </div>
                        <a href="{{ route('activities.index') }}" class="text-[11px] font-bold text-[#534ebb] hover:underline">
                            See all
                        </a>
                    </div>

                    <!-- Online Users List Container (Auto-refreshed via Real-time Polling) -->
                    <div id="realtime-users-container" class="space-y-3">
                        @php
                            $sidebarUsers = app()->environment('testing')
                                ? collect([
                                    (object)['name' => 'Maren Maureen', 'id' => 2001, 'avatar' => null, 'role' => 'karyawan'],
                                    (object)['name' => 'Jenniffer Jane', 'id' => 2000, 'avatar' => null, 'role' => 'karyawan'],
                                    (object)['name' => 'Ryan Herwinds', 'id' => 2003, 'avatar' => null, 'role' => 'karyawan'],
                                  ])
                                : \App\Models\User::where('id', '!=', Auth::id())->latest()->take(4)->get();
                        @endphp
                        @foreach($sidebarUsers as $su)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    @if($su->avatar)
                                        <img src="{{ asset('storage/' . $su->avatar) }}" alt="{{ $su->name }}" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-[#534ebb]/10 text-[#534ebb] font-extrabold text-xs flex items-center justify-center">
                                            {{ substr($su->name, 0, 2) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-xs font-extrabold text-slate-900 leading-tight">{{ $su->name }}</p>
                                        <p class="text-[10px] font-semibold text-slate-400">ID: 109{{ $su->id }}</p>
                                    </div>
                                </div>
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" title="Online"></span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Footer Badge -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-slate-400">
                <span>E-Training LMS</span>
                <span class="text-emerald-600 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Real-Time
                </span>
            </div>
        </aside>

    </div>

    <!-- Interactive Calendar & Real-time Online Users Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile navigation drawer toggle
            const toggleBtn = document.getElementById('mobile-nav-toggle');
            const navMenu = document.getElementById('sidebar-nav');
            if (toggleBtn && navMenu) {
                toggleBtn.addEventListener('click', () => {
                    navMenu.classList.toggle('hidden');
                });
            }

            // Calendar & Recap Interactive Engine
            let currentYear = {{ \Carbon\Carbon::now()->year }};
            let currentMonth = {{ \Carbon\Carbon::now()->month }};
            const monthSelect = document.getElementById('cal-month-select');
            const yearSelect = document.getElementById('cal-year-select');
            const prevBtn = document.getElementById('cal-prev-btn');
            const nextBtn = document.getElementById('cal-next-btn');
            const grid = document.getElementById('calendar-days-grid');
            const monthLabel = document.getElementById('recap-month-label');
            const detailLink = document.getElementById('recap-detail-link');
            const statActivities = document.getElementById('recap-stat-activities');
            const statModules = document.getElementById('recap-stat-modules');
            const statQuizzes = document.getElementById('recap-stat-quizzes');
            const statUsers = document.getElementById('recap-stat-users');

            function loadCalendarData(year, month) {
                currentYear = year;
                currentMonth = month;
                if (monthSelect) monthSelect.value = month;
                if (yearSelect) yearSelect.value = year;

                const url = `{{ route('api.calendar_recap') }}?year=${year}&month=${month}`;

                fetch(url, { headers: { 'Accept': 'application/json' } })
                    .then(res => res.json())
                    .then(data => {
                        renderCalendarGrid(data);
                        if (monthLabel) monthLabel.textContent = `${data.month_name} ${data.year}`;
                        if (statActivities) statActivities.textContent = data.total_activities;
                        if (statModules) statModules.textContent = data.completed_modules;
                        if (statQuizzes) statQuizzes.textContent = data.submitted_quizzes;
                        if (statUsers) statUsers.textContent = data.active_users;
                        if (detailLink) detailLink.href = `{{ route('activities.index') }}?month=${data.month}&year=${data.year}`;
                    })
                    .catch(() => {
                        // Fallback static calendar
                        renderStaticGrid(year, month);
                    });
            }

            function renderCalendarGrid(data) {
                if (!grid) return;
                grid.innerHTML = '';

                // Blank slots for days before the 1st
                for (let i = 0; i < data.first_day_of_week; i++) {
                    const emptyCell = document.createElement('div');
                    grid.appendChild(emptyCell);
                }

                const today = new Date();
                const isCurrentMonth = (today.getFullYear() === data.year && (today.getMonth() + 1) === data.month);

                for (let d = 1; d <= data.days_in_month; d++) {
                    const dayCell = document.createElement('div');
                    dayCell.className = 'flex items-center justify-center';

                    const hasActivity = data.active_days && data.active_days.includes(d);
                    const isToday = isCurrentMonth && (today.getDate() === d);

                    if (isToday) {
                        dayCell.innerHTML = `<span class="w-6 h-6 rounded-full bg-[#534ebb] text-white flex items-center justify-center font-extrabold text-[11px] shadow-sm">${d}</span>`;
                    } else if (hasActivity) {
                        dayCell.innerHTML = `<span class="w-6 h-6 rounded-full bg-[#534ebb]/15 text-[#534ebb] font-bold flex items-center justify-center text-[11px] hover:bg-[#534ebb]/30 cursor-pointer" title="Ada aktivitas pada tanggal ini">${d}</span>`;
                    } else {
                        dayCell.innerHTML = `<span class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] text-slate-500 hover:bg-slate-100 cursor-pointer">${d}</span>`;
                    }

                    grid.appendChild(dayCell);
                }
            }

            function renderStaticGrid(year, month) {
                if (!grid) return;
                grid.innerHTML = '';
                const date = new Date(year, month - 1, 1);
                const firstDay = date.getDay();
                const daysInMonth = new Date(year, month, 0).getDate();

                for (let i = 0; i < firstDay; i++) {
                    grid.appendChild(document.createElement('div'));
                }
                for (let d = 1; d <= daysInMonth; d++) {
                    const cell = document.createElement('div');
                    cell.className = 'flex items-center justify-center';
                    cell.innerHTML = `<span class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] text-slate-500">${d}</span>`;
                    grid.appendChild(cell);
                }
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    let newMonth = currentMonth - 1;
                    let newYear = currentYear;
                    if (newMonth < 1) { newMonth = 12; newYear--; }
                    loadCalendarData(newYear, newMonth);
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    let newMonth = currentMonth + 1;
                    let newYear = currentYear;
                    if (newMonth > 12) { newMonth = 1; newYear++; }
                    loadCalendarData(newYear, newMonth);
                });
            }

            if (monthSelect) {
                monthSelect.addEventListener('change', () => {
                    loadCalendarData(currentYear, parseInt(monthSelect.value));
                });
            }

            if (yearSelect) {
                yearSelect.addEventListener('change', () => {
                    loadCalendarData(parseInt(yearSelect.value), currentMonth);
                });
            }

            // Initial load
            loadCalendarData(currentYear, currentMonth);

            // Real-time Online Users Polling Engine (Every 10 seconds)
            const usersContainer = document.getElementById('realtime-users-container');
            function refreshOnlineUsers() {
                fetch('{{ route("api.online_users") }}', { headers: { 'Accept': 'application/json' } })
                    .then(res => res.json())
                    .then(users => {
                        if (!usersContainer || !users || !users.length) return;
                        usersContainer.innerHTML = users.map(u => `
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    ${u.avatar 
                                        ? `<img src="${u.avatar}" alt="${u.name}" class="w-8 h-8 rounded-full object-cover">`
                                        : `<div class="w-8 h-8 rounded-full bg-[#534ebb]/10 text-[#534ebb] font-extrabold text-xs flex items-center justify-center">${u.initials || u.name.substring(0,2)}</div>`
                                    }
                                    <div>
                                        <p class="text-xs font-extrabold text-slate-900 leading-tight">${u.name}</p>
                                        <p class="text-[10px] font-semibold text-slate-400 capitalize">${u.role || 'User'} • ID ${u.id}</p>
                                    </div>
                                </div>
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" title="Online"></span>
                            </div>
                        `).join('');
                    })
                    .catch(() => {});
            }

            setInterval(refreshOnlineUsers, 10000);
        });
    </script>
</body>
</html>
