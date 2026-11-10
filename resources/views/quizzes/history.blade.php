@extends('layouts.app')

@section('page_title', 'Hasil Kuis')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('courses.show', $quiz->course_id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-[#534ebb] transition">
            <span>← Kembali ke Detail Kursus</span>
        </a>
    </div>

    <!-- Score Summary Card -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-1.5 flex-1">
            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-[#534ebb]/10 text-[#534ebb]">
                Rangkuman Hasil Evaluasi
            </span>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $quiz->title }}</h2>
            <p class="text-xs font-semibold text-slate-400">Target kelulusan (passing grade): <strong>{{ $quiz->passing_grade }}%</strong></p>
            
            @if($isPendingTutor)
                <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                    <span>⚠️</span>
                    <span>Jawaban esai Anda sedang menunggu validasi review oleh Tutor.</span>
                </div>
            @endif
        </div>

        @php
            $hasEssay = $quiz->questions->where('type', 'essay')->isNotEmpty();
        @endphp

        <div class="flex flex-col items-center gap-2 shrink-0">
            @if($hasEssay && $isPendingTutor)
                <div class="h-28 w-28 rounded-3xl flex flex-col items-center justify-center bg-amber-50 text-amber-700 border border-amber-200 shadow-sm">
                    <span class="text-xs font-extrabold text-center leading-tight">Menunggu<br>Tutor</span>
                </div>
                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-extrabold">
                    REVIEW PENDING
                </span>
            @else
                <div class="h-28 w-28 rounded-3xl flex flex-col items-center justify-center {{ $passed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }} shadow-sm">
                    <span class="text-3xl font-extrabold">{{ $finalScore }}</span>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider">Skor</span>
                </div>
                <span class="px-3 py-1 rounded-full {{ $passed ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }} text-[11px] font-extrabold">
                    {{ $passed ? '✓ LULUS' : '✕ BELUM LULUS' }}
                </span>
            @endif
        </div>
    </div>

    <!-- Detailed Answers Review -->
    <div class="space-y-4">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Detail Jawaban & Penilaian</h3>
            <p class="text-xs font-semibold text-slate-400 mt-0.5">Umpan balik evaluasi AI dan catatan penyesuaian tutor per butir soal</p>
        </div>

        <div class="space-y-4">
            @foreach($userAnswers as $index => $ans)
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                    <div class="flex items-start gap-3.5">
                        <span class="w-8 h-8 rounded-2xl bg-indigo-50 text-[#534ebb] flex items-center justify-center font-extrabold text-xs shrink-0 shadow-inner">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-slate-100 text-slate-600">
                                {{ str_replace('_', ' ', $ans->question->type) }}
                            </span>
                            <h4 class="text-sm sm:text-base font-extrabold text-slate-900 mt-1 leading-snug">{{ $ans->question->question }}</h4>
                        </div>
                    </div>

                    <!-- Student Answer Box -->
                    <div class="p-4 rounded-2xl bg-[#fcfbfe] border border-slate-100 text-xs font-semibold text-slate-800">
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-1">Jawaban Anda:</span>
                        <p class="whitespace-pre-line leading-relaxed">{{ $ans->answer }}</p>
                    </div>

                    <!-- Evaluation & Feedback Box -->
                    <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-100 space-y-2.5 text-xs">
                        <div class="flex items-center justify-between font-bold">
                            <span class="text-slate-500">Nilai Perolehan:</span>
                            @if($ans->question->type === 'essay' && $ans->final_score === null)
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-extrabold">
                                    Menunggu Review Tutor (Skor AI: {{ $ans->ai_score }})
                                </span>
                            @else
                                <span class="text-base font-extrabold text-[#534ebb]">
                                    {{ $ans->final_score ?? $ans->ai_score }} / 100
                                </span>
                            @endif
                        </div>

                        <!-- Tutor Adjustment Record Details (Requirement 4) -->
                        @if($ans->essayReview && $ans->essayReview->tutor_score !== null)
                            <div class="p-3 rounded-xl bg-white border border-indigo-100 space-y-1">
                                <div class="flex items-center justify-between text-[11px] font-bold">
                                    <span class="text-indigo-700">👨‍🏫 Validasi Tutor: Skor Disesuaikan</span>
                                    <span class="text-slate-500">Skor Awal AI: {{ $ans->ai_score }} ➔ Skor Akhir: <strong class="text-indigo-900">{{ $ans->essayReview->tutor_score }}</strong></span>
                                </div>
                                @if($ans->essayReview->tutor_notes)
                                    <p class="text-[11px] text-slate-600 font-medium">Catatan: {{ $ans->essayReview->tutor_notes }}</p>
                                @endif
                            </div>
                        @endif

                        @if($ans->ai_feedback)
                            <div class="pt-2 border-t border-slate-200/60 text-slate-600">
                                <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5">Umpan Balik AI:</span>
                                <p class="font-medium whitespace-pre-line leading-relaxed">{{ $ans->ai_feedback }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
