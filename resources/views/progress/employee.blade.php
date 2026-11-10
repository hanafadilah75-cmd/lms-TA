@extends('layouts.app')

@section('page_title', 'Rapor Progres - ' . $employee->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div>
        @if(Auth::user()->isKaryawan())
            <!-- Karyawan stays on their dashboard -->
        @else
            <a href="{{ route('progress.company', $employee->company_id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition">
                <span>←</span>
                <span>Kembali ke Daftar Karyawan</span>
            </a>
        @endif
    </div>

    <!-- Employee Info Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm flex flex-col sm:flex-row items-center gap-6">
        @if($employee->avatar)
            <img class="h-20 w-20 rounded-2xl object-cover border border-slate-100 shadow-sm" src="{{ asset('storage/' . $employee->avatar) }}" alt="">
        @else
            <div class="h-20 w-20 rounded-2xl bg-[#534ebb]/10 text-[#534ebb] flex items-center justify-center font-extrabold text-2xl uppercase">
                {{ substr($employee->name, 0, 2) }}
            </div>
        @endif
        <div class="flex-1 text-center sm:text-left">
            <div class="flex flex-wrap items-center gap-2 justify-center sm:justify-start">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $employee->name }}</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-[#534ebb]/10 text-[#534ebb] capitalize">
                    {{ $employee->role }}
                </span>
            </div>
            <p class="text-xs font-semibold text-slate-400 mt-1.5">📧 {{ $employee->email }}</p>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">🏢 Perusahaan: <b>{{ optional($employee->company)->name ?? '-' }}</b></p>
        </div>
    </div>

    <!-- Detailed Course Progress List -->
    <div class="space-y-5">
        <div>
            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Progres per Materi Pelatihan</h3>
            <p class="text-xs font-semibold text-slate-400 mt-0.5">Rincian status modul dan evaluasi kuis peserta</p>
        </div>

        @if(count($courseProgress) > 0)
            @foreach($courseProgress as $cp)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
                    <!-- Course Info Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-100 text-slate-600">
                                    {{ $cp['course']->level }}
                                </span>
                                <span class="text-xs font-bold text-slate-400">Tutor: {{ $cp['course']->tutor?->name ?? 'Tutor Tidak Aktif' }}</span>
                            </div>
                            <h4 class="text-lg font-extrabold text-slate-900 mt-2">{{ $cp['course']->title }}</h4>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-full">
                                {{ $cp['completed_modules'] }} / {{ $cp['total_modules'] }} Modul
                            </span>
                            @if($cp['status'] === 'selesai')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700">
                                    ✓ Selesai
                                </span>
                            @elseif($cp['status'] === 'sedang_belajar')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-amber-50 text-amber-700">
                                    Sedang Belajar
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-600">
                                    Belum Mulai
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Course Progress Bar -->
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-slate-600 mb-2">
                            <span>Kemajuan Modul</span>
                            <span class="font-extrabold text-[#534ebb]">{{ $cp['progress_percent'] }}% Selesai</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden p-0.5">
                            <div class="h-full rounded-full transition-all duration-300 {{ $cp['progress_percent'] == 100 ? 'bg-emerald-500' : 'bg-[#534ebb]' }}" style="width: {{ $cp['progress_percent'] }}%"></div>
                        </div>
                    </div>

                    <!-- Quiz Status Detail Table -->
                    <div class="pt-2">
                        <h5 class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-3">Status Kuis Evaluasi</h5>
                        
                        @if(count($cp['quizzes']) > 0)
                            <div class="space-y-3">
                                @foreach($cp['quizzes'] as $quizStat)
                                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                                        <div>
                                            <div class="font-extrabold text-slate-900 text-sm">{{ $quizStat['quiz_title'] }}</div>
                                            <div class="text-slate-400 font-semibold mt-0.5">Passing Grade: {{ $quizStat['passing_grade'] }}%</div>
                                        </div>
                                        
                                        <div class="flex items-center gap-6">
                                            <!-- Score -->
                                            <div class="text-right">
                                                <span class="text-slate-400 font-bold">Skor:</span>
                                                @if(!empty($quizStat['has_essay']) && !empty($quizStat['pending']))
                                                    <span class="font-extrabold text-sm ml-1 text-amber-600">Belum Dinilai</span>
                                                @else
                                                    <span class="font-extrabold text-sm ml-1 text-[#534ebb]">{{ $quizStat['score'] }}</span>
                                                @endif
                                            </div>
                                            
                                            <!-- Status Badge -->
                                            @if(!empty($quizStat['has_essay']) && !empty($quizStat['pending']))
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-amber-50 text-amber-700">
                                                    Menunggu Review
                                                </span>
                                            @elseif(!empty($quizStat['has_essay']) && empty($quizStat['pending']))
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700">
                                                    Selesai Dinilai
                                                </span>
                                            @elseif(($quizStat['status'] ?? '') === 'Lulus')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700">
                                                    ✓ Lulus
                                                </span>
                                            @elseif(($quizStat['status'] ?? '') === 'Tidak Lulus')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-rose-50 text-rose-700">
                                                    ✕ Tidak Lulus
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-600">
                                                    Belum Dikerjakan
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs font-bold text-slate-400">Belum ada kuis evaluasi untuk materi kursus ini.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-100">
                <p class="text-sm font-bold text-slate-400">Belum ada materi pelatihan yang ditugaskan kepada karyawan ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection
