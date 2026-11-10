@extends('layouts.app')

@section('page_title', 'Detail Kuis & Pertanyaan')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs -->
    <div>
        <a href="{{ route('courses.show', $course->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition">
            <span>←</span>
            <span>Kembali ke Detail Kursus</span>
        </a>
    </div>

    <!-- Quiz Header -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-[#534ebb]/10 text-[#534ebb] uppercase">
                {{ str_replace('_', ' ', $quiz->type) }}
            </span>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-2">{{ $quiz->title }}</h2>
            <p class="text-xs font-semibold text-slate-400 mt-1">Passing Grade: {{ $quiz->passing_grade }}% | Durasi: {{ $quiz->duration }} menit</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('quizzes.questions.create', $quiz->id) }}" class="px-5 py-2.5 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-extrabold shadow-sm transition">
                ➕ Tambah Pertanyaan
            </a>
            <a href="{{ route('quizzes.edit', $quiz->id) }}" class="px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold transition">
                Edit Kuis
            </a>
        </div>
    </div>

    <!-- Questions List -->
    <div class="space-y-5">
        <div>
            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Daftar Pertanyaan ({{ $quiz->questions->count() }})</h3>
            <p class="text-xs font-semibold text-slate-400 mt-0.5">Pertanyaan yang akan dikerjakan oleh karyawan</p>
        </div>

        @forelse($quiz->questions as $index => $question)
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-xl bg-[#534ebb]/10 text-[#534ebb] flex items-center justify-center font-extrabold text-xs">
                                {{ $index + 1 }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-100 text-slate-600">
                                {{ $question->type }}
                            </span>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mt-3 leading-relaxed">
                            {{ $question->question }}
                        </h4>
                    </div>

                    <!-- Actions for Admin and Tutor -->
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('quizzes.questions.edit', [$quiz->id, $question->id]) }}" 
                           class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-extrabold text-slate-700 transition" 
                           title="Kustomisasi pertanyaan, opsi, dan kunci jawaban">
                            ✏️ Kustomisasi
                        </a>
                        <form action="{{ route('quizzes.questions.destroy', [$quiz->id, $question->id]) }}" method="POST" onsubmit="return confirm('Hapus pertanyaan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-xs font-extrabold text-rose-700 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Multiple choice options preview -->
                @if($question->type === 'multiple_choice')
                    <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($question->options as $optIndex => $opt)
                            <div class="p-3 rounded-2xl border flex items-center justify-between gap-2 text-xs font-bold {{ $opt->is_correct ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900' : 'bg-slate-50 border-slate-100 text-slate-600' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="h-6 w-6 rounded-full flex items-center justify-center text-[10px] shrink-0 font-extrabold {{ $opt->is_correct ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $opt->is_correct ? '✓' : chr(65 + $optIndex) }}
                                    </span>
                                    <span class="truncate">{{ $opt->option_text }}</span>
                                </div>
                                @if($opt->is_correct)
                                    <span class="px-2.5 py-0.5 text-[9px] font-extrabold bg-emerald-100 rounded-full text-emerald-800 shrink-0 uppercase tracking-wide">
                                        Kunci Benar
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Essay Ideal Answer / Rubric Preview -->
                @if($question->type === 'essay')
                    <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                        @if($question->ideal_answer)
                            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-xs font-semibold text-slate-800">
                                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 mb-1">
                                    🔑 Kunci Jawaban Acuan / Rubrik Penilaian (Dikustomisasi):
                                </span>
                                {{ $question->ideal_answer }}
                            </div>
                        @else
                            <div class="p-3 rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-xs font-bold text-slate-400 flex items-center justify-between">
                                <span>Belum ada kunci jawaban acuan.</span>
                                <a href="{{ route('quizzes.questions.edit', [$quiz->id, $question->id]) }}" class="text-xs text-[#534ebb] hover:underline font-extrabold">
                                    + Tambah Kunci Acuan
                                </a>
                            </div>
                        @endif

                        @if($question->keywords)
                            <div class="p-3 rounded-2xl bg-indigo-50/60 border border-indigo-100 text-xs font-semibold text-slate-700">
                                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#534ebb] mb-1">
                                    🏷️ Kata Kunci Soal Esai untuk AI Evaluator:
                                </span>
                                <span class="font-extrabold text-slate-900">{{ $question->keywords }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-100 text-slate-400 font-bold text-sm">
                Belum ada pertanyaan dalam kuis ini.
            </div>
        @endforelse
    </div>
</div>
@endsection
