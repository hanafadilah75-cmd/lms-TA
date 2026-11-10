@extends('layouts.app')

@section('page_title', 'Detail Kursus')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div>
        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-[#534ebb] transition">
            <span>← Kembali ke Semua Kursus</span>
        </a>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Main: Course Overview & Modules List -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Course Header Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-[#534ebb]/10 text-[#534ebb]">
                            {{ $course->level }}
                        </span>
                        <span class="text-xs font-bold text-slate-400">
                            Tutor: <strong class="text-slate-700">{{ optional($course->tutor)->name ?? 'Tutor' }}</strong>
                        </span>
                    </div>

                    @if(Auth::user()->isAdmin() || Auth::user()->isTutor())
                        <div class="flex items-center gap-2">
                            <a href="{{ route('courses.edit', $course->id) }}" class="px-3 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition">
                                ✏️ Edit Kursus
                            </a>
                            <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kursus ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-full bg-rose-50 hover:bg-rose-100 text-xs font-bold text-rose-700 transition">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight leading-snug">{{ $course->title }}</h2>
                <p class="text-xs sm:text-sm font-semibold text-slate-500 leading-relaxed">{{ $course->description ?? 'Kurikulum pelatihan komprehensif.' }}</p>

                @if($course->companies && $course->companies->isNotEmpty())
                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500">
                        <span class="font-extrabold text-slate-400">Ditugaskan ke Perusahaan Klien:</span>
                        @foreach($course->companies as $comp)
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 font-bold text-slate-700 text-[11px]">
                                🏢 {{ $comp->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Modules List -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-[#fcfbfe] flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Daftar Modul Materi</h3>
                        <p class="text-xs font-semibold text-slate-400">Pelajari modul materi per halaman</p>
                    </div>
                    @if(Auth::user()->isAdmin() || Auth::user()->isTutor())
                        <a href="{{ route('modules.create', $course->id) }}" class="px-4 py-2 rounded-full bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-bold shadow-sm transition">
                            ➕ Tambah Modul
                        </a>
                    @endif
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($course->modules as $index => $module)
                        @php
                            $isCompleted = in_array($module->id, $completedModuleIds ?? []);
                            $pagesCount = max(1, (int) ($module->total_pages ?? 1));
                        @endphp
                        <div class="p-5 sm:p-6 flex items-center justify-between gap-4 hover:bg-slate-50/70 transition">
                            <div class="flex items-center gap-4 min-w-0">
                                @if(Auth::user()->isKaryawan() && $isCompleted)
                                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold text-base shrink-0">
                                        ✓
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center font-extrabold text-xs shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <h4 class="text-sm sm:text-base font-extrabold text-slate-900 truncate">
                                        <a href="{{ route('modules.show', [$course->id, $module->id]) }}" class="hover:text-[#534ebb] transition">
                                            {{ $module->title }}
                                        </a>
                                    </h4>
                                    <p class="text-xs font-bold text-slate-400 mt-0.5">
                                        Modul #{{ $module->order_number }} • <strong>{{ $pagesCount }} Halaman</strong>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('modules.show', [$course->id, $module->id]) }}" 
                                   class="px-4 py-2 rounded-full bg-slate-100 hover:bg-[#534ebb] hover:text-white text-xs font-bold text-slate-700 transition">
                                    Pelajari ➔
                                </a>
                                
                                @if(Auth::user()->isAdmin() || Auth::user()->isTutor())
                                    <a href="{{ route('modules.edit', $module->id) }}" class="p-2 rounded-full bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition" title="Edit">
                                        ✏️
                                    </a>
                                    <form action="{{ route('modules.destroy', $module->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus modul ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-full bg-rose-50 hover:bg-rose-100 text-xs font-bold text-rose-700 transition" title="Hapus">
                                            🗑️
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs font-bold text-slate-400">
                            Belum ada modul materi dalam kursus ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar: Quizzes List -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Kuis Evaluasi</h3>
                        <p class="text-xs font-semibold text-slate-400">Evaluasi pemahaman materi</p>
                    </div>
                    @if(Auth::user()->isAdmin() || Auth::user()->isTutor())
                        <a href="{{ route('quizzes.create', $course->id) }}" class="px-3 py-1.5 rounded-full bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-bold shadow-sm transition">
                            ➕ Buat Kuis
                        </a>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse($course->quizzes as $quiz)
                        @php
                            $hasTakenQuiz = false;
                            if(Auth::user()->isKaryawan()) {
                                $qQuestionIds = $quiz->questions->pluck('id');
                                $qAnswer = \App\Models\UserAnswer::where('user_id', Auth::id())
                                    ->whereIn('question_id', $qQuestionIds)
                                    ->get();
                                $hasTakenQuiz = $qAnswer->isNotEmpty();
                            }
                            
                            $totalCourseModules = $course->modules->count();
                            $completedCourseModules = count($completedModuleIds ?? []);
                            $allModulesCompleted = ($totalCourseModules > 0 && $completedCourseModules === $totalCourseModules);
                        @endphp
                        <div class="p-4 rounded-2xl bg-[#fcfbfe] border border-slate-100 space-y-3">
                            <div>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-indigo-50 text-[#534ebb]">
                                    {{ str_replace('_', ' ', $quiz->type) }}
                                </span>
                                <h4 class="text-sm font-extrabold text-slate-900 mt-1.5">{{ $quiz->title }}</h4>
                                <p class="text-[11px] font-semibold text-slate-400 mt-0.5">Passing Grade: {{ $quiz->passing_grade }}% • Durasi: {{ $quiz->duration }}m</p>
                            </div>

                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                                @if(Auth::user()->isAdmin() || Auth::user()->isTutor())
                                    <a href="{{ route('courses.quizzes.show', [$course->id, $quiz->id]) }}" class="text-xs font-extrabold text-[#534ebb] hover:underline">
                                        Detail ({{ $quiz->questions->count() }} Soal) ➔
                                    </a>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('quizzes.edit', $quiz->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">Edit</a>
                                        <form action="{{ route('quizzes.destroy', $quiz->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kuis ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-rose-600 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                @else
                                    @if($hasTakenQuiz)
                                        <a href="{{ route('quizzes.history', $quiz->id) }}" class="w-full text-center py-2 rounded-full border border-slate-200 bg-white text-xs font-extrabold text-slate-800 hover:bg-slate-50 transition">
                                            Lihat Hasil Kuis ➔
                                        </a>
                                    @else
                                        @if($allModulesCompleted)
                                            <a href="{{ route('quizzes.take', $quiz->id) }}" class="w-full text-center py-2 rounded-full bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-extrabold shadow-sm transition">
                                                Kerjakan Kuis Sekarang ➔
                                            </a>
                                        @else
                                            <div class="w-full text-center py-2 rounded-full bg-slate-100 text-slate-400 text-xs font-bold">
                                                🔒 Selesaikan Modul Terlebih Dahulu
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-xs font-semibold text-slate-400 py-4">
                            Belum ada kuis untuk kursus ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
