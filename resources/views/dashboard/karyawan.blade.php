@extends('layouts.app')

@section('page_title', 'Dashboard Karyawan')

@section('content')
<div class="space-y-8">
    <!-- Overall Progress Banner Card -->
    <div class="bg-gradient-to-r from-[#534ebb] to-[#6b62d8] rounded-[32px] p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <!-- Subtle background circles matching photo -->
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-white/10 pointer-events-none"></div>
        <div class="absolute right-32 -top-12 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative z-10">
            <div class="space-y-1.5 max-w-xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-white/20 backdrop-blur-sm text-white">
                    🎯 Target Pelatihan Karyawan
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white mt-1">
                    Halo, {{ Auth::user()->name }}! 👋
                </h2>
                <p class="text-xs sm:text-sm font-medium text-white/80 leading-relaxed">
                    Progres pembelajaran dihitung berdasarkan <strong>akumulasi halaman materi</strong> yang telah Anda pelajari.
                </p>
            </div>

            <!-- Percentage Pill -->
            <div class="flex items-center gap-4 bg-white/15 backdrop-blur-md px-5 py-3.5 rounded-3xl border border-white/20 shrink-0">
                <div class="text-right">
                    <span class="block text-3xl sm:text-4xl font-extrabold text-white leading-none">{{ $overallProgressPercent }}%</span>
                    <span class="text-[10px] font-bold text-white/70 uppercase tracking-wider">Halaman Selesai</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white text-[#534ebb] flex items-center justify-center text-xl font-bold shadow-md">
                    🚀
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-black/20 rounded-full h-3 mt-6 overflow-hidden p-0.5">
            <div class="h-full rounded-full transition-all duration-700 bg-white" style="width: {{ $overallProgressPercent }}%"></div>
        </div>
    </div>

    <!-- Main Grid: My Courses (Reference Photo Style) & Recent Quizzes -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2 Cols: My Courses Cards -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Materi Kursus Saya</h3>
                    <p class="text-xs font-semibold text-slate-400">Daftar pelatihan aktif yang ditugaskan untuk perusahaan Anda</p>
                </div>
                <a href="{{ route('courses.index') }}" class="text-xs font-bold text-[#534ebb] hover:underline">
                    Lihat Semua ➔
                </a>
            </div>

            @php
                $cardStyles = [
                    ['bg' => 'bg-white', 'border' => 'border-slate-100', 'btn' => 'bg-[#6b62d8] hover:bg-[#534ebb]', 'device_bg' => 'bg-[#f0edfd]'],
                    ['bg' => 'bg-[#f5efff]', 'border' => 'border-purple-100', 'btn' => 'bg-[#a37cf0] hover:bg-[#8d61e6]', 'device_bg' => 'bg-[#ece4fc]'],
                    ['bg' => 'bg-[#fff0f2]', 'border' => 'border-rose-100', 'btn' => 'bg-[#f47285] hover:bg-[#e15469]', 'device_bg' => 'bg-[#ffe4e7]'],
                ];
            @endphp

            @if(count($activeCourses) > 0)
                <div class="space-y-4">
                    @foreach($activeCourses as $idx => $ac)
                        @php
                            $style = $cardStyles[$idx % count($cardStyles)];
                        @endphp
                        <div class="{{ $style['bg'] }} rounded-3xl p-6 border {{ $style['border'] }} shadow-sm hover:shadow-md transition-all duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                            
                            <!-- Left: Device Mockup Box & Info -->
                            <div class="flex items-center gap-5 flex-1 min-w-0">
                                <div class="w-24 h-20 rounded-2xl {{ $style['device_bg'] }} p-2.5 flex flex-col items-center justify-center shrink-0 shadow-inner">
                                    <span class="text-2xl">💻</span>
                                    <div class="flex gap-1 mt-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#534ebb]/50"></span>
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#534ebb]/20"></span>
                                    </div>
                                </div>

                                <div class="space-y-1 flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-white/80 border border-slate-200/60 text-slate-700">
                                            {{ $ac['course']->level }}
                                        </span>
                                        <span class="text-[11px] font-bold text-slate-500">
                                            📖 {{ $ac['pages_read'] ?? 0 }} / {{ $ac['total_pages'] ?? 1 }} Halaman
                                        </span>
                                    </div>

                                    <h4 class="text-base font-extrabold text-slate-900 tracking-tight leading-snug truncate">
                                        <a href="{{ route('courses.show', $ac['course']->id) }}" class="hover:text-[#534ebb] transition">
                                            {{ $ac['course']->title }}
                                        </a>
                                    </h4>

                                    <!-- Progress Bar -->
                                    <div class="w-full bg-slate-200/60 rounded-full h-2 overflow-hidden mt-2">
                                        <div class="h-full rounded-full bg-[#534ebb] transition-all duration-500" style="width: {{ $ac['progress'] }}%"></div>
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1">
                                        Progres Halaman: <strong class="text-slate-700">{{ $ac['progress'] }}% Selesai</strong>
                                    </p>
                                </div>
                            </div>

                            <!-- Right: Arrow Button -->
                            <div class="flex items-center justify-end sm:justify-center shrink-0">
                                <a href="{{ route('courses.show', $ac['course']->id) }}" 
                                    class="w-10 h-10 rounded-full {{ $style['btn'] }} text-white flex items-center justify-center shadow-md hover:scale-105 transition"
                                    title="Lanjut Belajar">
                                    <svg class="w-5 h-5 ml-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </a>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl p-10 text-center border border-slate-100">
                    <p class="text-xs font-semibold text-slate-400 mb-3">Belum ada materi aktif yang sedang Anda pelajari.</p>
                    <a href="{{ route('courses.index') }}" class="px-5 py-2.5 rounded-full bg-[#534ebb] text-white text-xs font-bold inline-block shadow-sm">
                        Mulai Belajar Sekarang ➔
                    </a>
                </div>
            @endif
        </div>

        <!-- Right Col: Recent Quizzes & Personal Activity -->
        <div class="space-y-6">
            <!-- Recent Quiz Scores -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Riwayat Kuis</h3>
                    <span class="text-xs font-bold text-slate-400">Hasil Evaluasi</span>
                </div>

                @if(count($latestScores) > 0)
                    <div class="space-y-3">
                        @foreach($latestScores as $score)
                            <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-xs font-extrabold text-slate-900 truncate">{{ $score['quiz_title'] }}</h4>
                                        <p class="text-[10px] font-semibold text-slate-400 truncate">{{ $score['course_title'] }}</p>
                                    </div>
                                    @if($score['pending'])
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">
                                            Review Tutor
                                        </span>
                                    @elseif($score['score'] >= $score['passing_grade'])
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                            Lulus
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800">
                                            Ulangi
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-50 font-bold">
                                    <span class="text-slate-400 font-semibold">Skor Akhir:</span>
                                    <span class="text-sm font-extrabold text-[#534ebb]">{{ $score['score'] }} / 100</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-2xl p-6 text-center border border-slate-100 text-slate-400 text-xs font-semibold">
                        Belum ada kuis yang dikerjakan.
                    </div>
                @endif
            </div>

            <!-- Recent Learning Activity -->
            <div class="bg-[#fcfbfe] rounded-3xl p-5 border border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Aktivitas Terakhir Anda</h4>
                    <a href="{{ route('activities.index') }}" class="text-[11px] font-bold text-[#534ebb] hover:underline">Semua</a>
                </div>

                @if(isset($recentActivities) && $recentActivities->isNotEmpty())
                    <div class="space-y-2.5">
                        @foreach($recentActivities as $act)
                            <div class="text-[11px] text-slate-600 bg-white p-2.5 rounded-xl border border-slate-100">
                                <p class="font-bold text-slate-800 leading-snug">{{ $act->description }}</p>
                                <span class="text-[10px] text-slate-400 font-semibold mt-0.5 block">{{ $act->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-[11px] text-slate-400 font-semibold text-center py-2">Belum ada riwayat aktivitas.</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
