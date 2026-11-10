@extends('layouts.app')

@section('page_title', 'Log Aktivitas')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#534ebb]/10 text-[#534ebb]">
                    Audit & Monitoring
                </span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900">Log Aktivitas Sistem</h2>
            <p class="text-xs font-semibold text-slate-500 mt-1">Rekam jejak seluruh tindakan pembelajaran, evaluasi, dan interaksi pengguna.</p>
        </div>

        @if(!Auth::user()->isKaryawan())
            <form action="{{ route('activities.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <select name="action" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 rounded-2xl px-3 py-2 focus:ring-2 focus:ring-[#534ebb] focus:outline-none">
                    <option value="">Semua Aksi</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="page_read" {{ request('action') == 'page_read' ? 'selected' : '' }}>Baca Halaman</option>
                    <option value="module_completed" {{ request('action') == 'module_completed' ? 'selected' : '' }}>Modul Selesai</option>
                    <option value="quiz_submitted" {{ request('action') == 'quiz_submitted' ? 'selected' : '' }}>Kuis Selesai</option>
                    <option value="score_adjusted" {{ request('action') == 'score_adjusted' ? 'selected' : '' }}>Skor Disesuaikan</option>
                    <option value="course_created" {{ request('action') == 'course_created' ? 'selected' : '' }}>Kursus Dibuat</option>
                </select>

                @if($allUsers->isNotEmpty())
                    <select name="user_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 rounded-2xl px-3 py-2 focus:ring-2 focus:ring-[#534ebb] focus:outline-none">
                        <option value="">Semua Pengguna</option>
                        @foreach($allUsers as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                        @endforeach
                    </select>
                @endif
            </form>
        @endif
    </div>

    <!-- Activities Timeline Table / Card -->
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-100">
        @if($logs->count() > 0)
            <div class="space-y-4">
                @foreach($logs as $log)
                    <div class="flex items-start gap-4 p-4 rounded-2xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50/50 transition duration-150">
                        <!-- Icon Circle -->
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 font-bold text-sm
                            @if(str_contains($log->action, 'login')) bg-emerald-100 text-emerald-700
                            @elseif(str_contains($log->action, 'score')) bg-indigo-100 text-[#534ebb]
                            @elseif(str_contains($log->action, 'quiz')) bg-amber-100 text-amber-800
                            @elseif(str_contains($log->action, 'module') || str_contains($log->action, 'page')) bg-sky-100 text-sky-700
                            @elseif(str_contains($log->action, 'course')) bg-purple-100 text-purple-700
                            @else bg-slate-100 text-slate-700 @endif">
                            @if(str_contains($log->action, 'login')) 🔑
                            @elseif(str_contains($log->action, 'score')) 🎯
                            @elseif(str_contains($log->action, 'quiz')) 📝
                            @elseif(str_contains($log->action, 'page')) 📖
                            @elseif(str_contains($log->action, 'module')) ✅
                            @elseif(str_contains($log->action, 'course')) 📚
                            @else ⚡ @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-extrabold text-slate-900 truncate">
                                        {{ $log->user?->name ?? 'Pengguna Sistem' }}
                                    </span>
                                    @if($log->user)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                            @if($log->user->role === 'admin') bg-rose-100 text-rose-700
                                            @elseif($log->user->role === 'tutor') bg-amber-100 text-amber-800
                                            @else bg-blue-100 text-blue-700 @endif">
                                            {{ $log->user->role }}
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[11px] font-semibold text-slate-400">
                                    {{ $log->created_at->diffForHumans() }} ({{ $log->created_at->format('d M Y, H:i') }})
                                </span>
                            </div>

                            <p class="text-xs font-semibold text-slate-600 leading-relaxed">
                                {{ $log->description }}
                            </p>

                            @if($log->ip_address)
                                <div class="mt-1 flex items-center gap-3 text-[10px] text-slate-400 font-medium">
                                    <span>IP: {{ $log->ip_address }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-3xl mb-2">📜</div>
                <h4 class="text-sm font-bold text-slate-700">Belum Ada Catatan Log Aktivitas</h4>
                <p class="text-xs font-semibold text-slate-400 mt-1">Aktivitas pembelajaran dan tindakan sistem akan terekam secara otomatis di sini.</p>
            </div>
        @endif
    </div>
</div>
@endsection

