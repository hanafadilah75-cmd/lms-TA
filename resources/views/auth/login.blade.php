<!DOCTYPE html>
<html lang="id" class="h-full bg-[#eceaf9]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem E-Training LMS</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #eceaf9;
        }
    </style>

    <!-- Vite Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 text-slate-800">
    <div class="w-full max-w-md space-y-6">
        <!-- Brand Header -->
        <div class="text-center">
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">
                Sistem E-Training LMS
            </h2>
            <p class="mt-1.5 text-xs font-semibold text-slate-400">
                Platform Pelatihan & Evaluasi Karyawan
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-xl shadow-[#534ebb]/10">
            <!-- Toast notification for logout or errors -->
            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-100 text-xs font-bold text-emerald-800 text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-100 text-xs font-bold text-rose-800 text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form class="space-y-5" action="{{ route('login.post') }}" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                        Alamat Email
                    </label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                        placeholder="nama@perusahaan.com"
                        class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                </div>

                <div>
                    <label for="password" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                        Password
                    </label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        placeholder="••••••••"
                        class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer font-semibold text-slate-600">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded-md border-slate-300 text-[#534ebb] focus:ring-[#534ebb]">
                        Ingat Saya
                    </label>
                    <span class="text-slate-400 font-semibold">LMS Terintegrasi</span>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-white font-extrabold text-sm shadow-md transition">
                        Masuk ke Akun ➔
                    </button>
                </div>
            </form>
        </div>

        <!-- Role Badges bottom pill -->
        <div class="p-4 rounded-2xl bg-white/80 border border-slate-100 flex items-center justify-around text-xs font-bold text-slate-600 shadow-sm">
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-[#534ebb]"></span>
                <span>Admin</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <span>Tutor</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span>Karyawan</span>
            </div>
        </div>
    </div>
</body>
</html>
