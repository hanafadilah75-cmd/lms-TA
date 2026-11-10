@extends('layouts.app')

@section('page_title', 'Review Lembar Jawaban Esai')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('reviews.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition">
            <span>←</span>
            <span>Kembali ke Daftar Review</span>
        </a>
    </div>

    <!-- Submission Details Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
        <!-- Employee Meta -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-[#534ebb]/10 text-[#534ebb] mb-1">
                    Lembar Jawaban
                </span>
                <h3 class="text-xl font-extrabold text-slate-900 mt-1">{{ $answer->user?->name ?? 'Karyawan Tidak Aktif' }}</h3>
                <p class="text-xs font-semibold text-slate-400">🏢 Perusahaan: {{ $answer->user?->company?->name ?? 'Perusahaan Tidak Aktif' }}</p>
            </div>
            <div class="sm:text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 mb-1">
                    Materi Kursus
                </span>
                <h4 class="text-sm font-extrabold text-slate-900 mt-1">{{ $answer->question->quiz->course->title }}</h4>
                <p class="text-xs font-semibold text-slate-400">{{ $answer->question->quiz->title }}</p>
            </div>
        </div>

        <!-- Question -->
        <div>
            <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Pertanyaan Kuis:</span>
            <p class="text-base font-extrabold text-slate-900 mt-1.5 leading-relaxed">{{ $answer->question->question }}</p>

            @if($answer->question->ideal_answer)
                <div class="mt-3 p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-xs font-semibold text-slate-800">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 mb-0.5">
                        🔑 Kunci Jawaban Acuan / Rubrik Penilaian (Tutor/Admin):
                    </span>
                    {{ $answer->question->ideal_answer }}
                </div>
            @endif

            @if($answer->question->keywords)
                <div class="mt-3 p-3 rounded-2xl bg-indigo-50/60 border border-indigo-100 text-xs font-semibold text-slate-700">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-[#534ebb] mb-1">
                        🏷️ Kata Kunci Penilaian AI yang Ditetapkan:
                    </span>
                    <div class="flex flex-wrap gap-1.5 mt-1">
                        @foreach(array_map('trim', explode(',', $answer->question->keywords)) as $kw)
                            @if(!empty($kw))
                                <span class="px-2.5 py-0.5 rounded-lg bg-white border border-indigo-200 text-[#534ebb] font-bold text-[11px]">{{ $kw }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Answer -->
        <div>
            <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Jawaban Karyawan:</span>
            <div class="mt-2 p-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm font-semibold text-slate-800 leading-relaxed">
                {{ $answer->answer }}
            </div>
        </div>
    </div>

    <!-- AI Evaluation Details Card -->
    <div class="bg-indigo-50/40 rounded-3xl p-6 sm:p-8 border border-indigo-100/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-indigo-100 pb-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-[#534ebb] text-white">
                🤖 Umpan Balik AI
            </span>
            <span class="text-base font-extrabold text-[#534ebb]">
                Skor AI: {{ $answer->ai_score }} / 100
            </span>
        </div>
        <div class="text-sm text-slate-700 font-semibold leading-relaxed whitespace-pre-line">
            {{ $answer->ai_feedback }}
        </div>
    </div>

    <!-- Tutor Grading Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
        <h3 class="text-lg font-extrabold text-slate-900 mb-1">Finalisasi Nilai Tutor</h3>
        <p class="text-xs font-semibold text-slate-400 mb-6">Masukkan nilai akhir Anda (dapat mengadopsi atau mengubah nilai AI).</p>

        <form action="{{ route('reviews.finalize', $answer->id) }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="tutor_score" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-2">
                    Nilai Akhir (0 - 100)
                </label>
                <div class="relative max-w-xs">
                    <input type="number" step="0.1" min="0" max="100" name="tutor_score" id="tutor_score" required
                        value="{{ old('tutor_score', $answer->final_score ?? $answer->ai_score) }}"
                        class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-xl font-extrabold text-slate-900 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                </div>
            </div>

            <div>
                <label for="tutor_notes" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-2">
                    Catatan & Masukan untuk Karyawan (Opsional)
                </label>
                <textarea name="tutor_notes" id="tutor_notes" rows="4"
                    placeholder="Tuliskan catatan apresiasi atau perbaikan untuk karyawan..."
                    class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition leading-relaxed">{{ old('tutor_notes', $answer->essayReview->tutor_notes ?? '') }}</textarea>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-7 py-3 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-extrabold shadow-sm transition">
                    Simpan & Finalisasi Nilai ➔
                </button>
            </div>
        </form>
    </div>

    <!-- Detailed Tutor Score Adjustment Record / Audit Trail -->
    @if($answer->scoreAdjustmentLogs && $answer->scoreAdjustmentLogs->isNotEmpty())
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-extrabold text-slate-900">📋 Record Detail Penyesuaian Skor Tutor</h4>
                    <p class="text-xs font-semibold text-slate-400">Riwayat audit perubahan nilai yang dilakukan oleh pengajar/tutor.</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-extrabold">
                    {{ $answer->scoreAdjustmentLogs->count() }} Kali Penyesuaian
                </span>
            </div>

            <div class="space-y-3">
                @foreach($answer->scoreAdjustmentLogs as $log)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-extrabold text-slate-900">
                                👨‍🏫 {{ $log->tutor?->name ?? 'Tutor / Admin' }}
                            </span>
                            <span class="text-slate-400 font-semibold text-[11px]">
                                {{ $log->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-bold pt-1">
                            <span class="text-slate-400">Skor Awal AI: <span class="text-slate-800">{{ $log->original_ai_score ?? '-' }}</span></span>
                            <span>➔</span>
                            <span class="text-[#534ebb] font-extrabold">Skor Akhir Disesuaikan: {{ $log->adjusted_score }}</span>
                            @if($log->original_ai_score !== null)
                                @php $diff = $log->adjusted_score - $log->original_ai_score; @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $diff >= 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $diff >= 0 ? "+{$diff}" : $diff }}
                                </span>
                            @endif
                        </div>
                        @if($log->notes)
                            <div class="text-xs text-slate-600 font-semibold bg-white p-3 rounded-xl border border-slate-100 mt-1">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Catatan Tutor:</span>
                                {{ $log->notes }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
