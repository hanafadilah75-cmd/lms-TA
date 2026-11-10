@extends('layouts.app')

@section('page_title', 'Kustomisasi Soal & Kunci Jawaban')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb / Back button -->
    <div>
        <a href="{{ route('courses.quizzes.show', [$quiz->course_id, $quiz->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition">
            <span>←</span>
            <span>Kembali ke Daftar Soal Kuis</span>
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-[#534ebb]/10 text-[#534ebb] uppercase mb-2">
                {{ str_replace('_', ' ', $quiz->type) }} | Kuis: {{ $quiz->title }}
            </span>
            <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">✏️ Kustomisasi Soal &amp; Kunci Jawaban</h3>
            <p class="text-xs font-semibold text-slate-400 mt-1">
                Sebagai <b>{{ ucfirst(Auth::user()->role) }}</b>, Anda memiliki hak penuh untuk mengubah pertanyaan, opsi pilihan ganda, menetapkan kunci jawaban yang benar, atau merumuskan rubrik jawaban esai ideal.
            </p>
        </div>

        <form action="{{ route('quizzes.questions.update', [$quiz->id, $question->id]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Question Text -->
            <div>
                <label for="question" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">
                    Kalimat Pertanyaan <span class="text-rose-500">*</span>
                </label>
                <textarea name="question" id="question" required rows="4" 
                    placeholder="Ketik kalimat pertanyaan lengkap di sini..."
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition leading-relaxed font-semibold">{{ old('question', $question->question) }}</textarea>
                @error('question')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Question Type Switcher -->
            <div>
                <label for="type" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">
                    Tipe Pertanyaan <span class="text-rose-500">*</span>
                </label>
                <select name="type" id="type" required onchange="toggleOptions()" class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition font-bold">
                    <option value="multiple_choice" {{ old('type', $question->type) === 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda (Multiple Choice)</option>
                    <option value="essay" {{ old('type', $question->type) === 'essay' ? 'selected' : '' }}>Esai (Essay)</option>
                </select>
                @error('type')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Multiple Choice Options Customization Container -->
            <div id="options-container" class="space-y-4 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Kustomisasi Opsi Jawaban & Kunci Benar</h4>
                        <p class="text-[11px] font-semibold text-slate-400">
                            Pilih radio button pada opsi yang menjadi <b>Kunci Jawaban yang Benar</b>.
                        </p>
                    </div>
                    <button type="button" onclick="addOptionRow()" class="px-3 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-xs font-extrabold text-slate-700 transition">
                        ➕ Tambah Opsi
                    </button>
                </div>

                <div id="options-list" class="space-y-3">
                    @php
                        $options = $question->options;
                        $count = max(count($options), 4);
                    @endphp

                    @for($i = 0; $i < $count; $i++)
                        @php
                            $opt = $options[$i] ?? null;
                            $isCorrect = old('correct_option', $opt?->is_correct ? $i : 0) == $i;
                            $val = old("options.{$i}", $opt?->option_text ?? '');
                        @endphp
                        <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 option-row">
                            <!-- Radio button for correct option -->
                            <div class="flex items-center gap-1.5">
                                <input type="radio" name="correct_option" value="{{ $i }}" {{ $isCorrect ? 'checked' : '' }}
                                    class="w-5 h-5 border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" 
                                    title="Tandai sebagai kunci jawaban yang benar">
                                <span class="text-xs font-extrabold text-slate-700 w-5 text-center">{{ chr(65 + $i) }}</span>
                            </div>

                            <div class="flex-1">
                                <input type="text" name="options[]" placeholder="Teks Pilihan {{ chr(65 + $i) }}" value="{{ $val }}" required
                                    class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                            </div>
                        </div>
                    @endfor
                </div>

                @error('options')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Essay Ideal Answer / Rubrik Customization Container -->
            <div id="ideal-answer-container" class="space-y-4 pt-4 border-t border-slate-100" style="display: none;">
                <div>
                    <label for="ideal_answer" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">
                        🔑 Kunci Jawaban Acuan / Rubrik Penilaian Esai
                    </label>
                    <p class="text-[11px] font-semibold text-slate-400 mb-2">
                        Rumuskan poin-poin kunci jawaban ideal yang wajib dijawab karyawan sebagai acuan AI dan Tutor.
                    </p>
                    <textarea name="ideal_answer" id="ideal_answer" rows="5" 
                        placeholder="Tuliskan poin-poin kunci jawaban ideal atau kata kunci penting yang harus ada dalam jawaban karyawan..."
                        class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition leading-relaxed font-semibold">{{ old('ideal_answer', $question->ideal_answer) }}</textarea>
                    @error('ideal_answer')
                        <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="keywords" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">
                        🏷️ Kata Kunci Soal Esai (Dilempar ke Prompt AI) <span class="text-xs text-slate-400 font-semibold">(Opsional)</span>
                    </label>
                    <p class="text-[11px] font-semibold text-slate-400 mb-1.5">Kata kunci penting yang wajib dinilai AI dalam jawaban peserta (pisahkan dengan koma). Parameter ini dilempar langsung ke evaluasi AI dan Fallback System.</p>
                    <input type="text" name="keywords" id="keywords" value="{{ old('keywords', $question->keywords) }}" placeholder="Contoh: MVC, Controller, Model, Dependency Injection"
                        class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition font-semibold">
                    @error('keywords')
                        <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('courses.quizzes.show', [$quiz->course_id, $quiz->id]) }}" class="px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-xs font-extrabold text-slate-700 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-xs font-extrabold text-white shadow-sm transition">
                    💾 Simpan Kustomisasi Jawaban ➔
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleOptions() {
        const typeSelect = document.getElementById('type');
        const optionsContainer = document.getElementById('options-container');
        const idealAnswerContainer = document.getElementById('ideal-answer-container');
        if (typeSelect.value === 'essay') {
            optionsContainer.style.display = 'none';
            idealAnswerContainer.style.display = 'block';
        } else {
            optionsContainer.style.display = 'block';
            idealAnswerContainer.style.display = 'none';
        }
    }

    function addOptionRow() {
        const list = document.getElementById('options-list');
        const currentCount = list.children.length;
        const letter = String.fromCharCode(65 + currentCount);

        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 option-row';
        row.innerHTML = `
            <div class="flex items-center gap-1.5">
                <input type="radio" name="correct_option" value="${currentCount}" 
                    class="w-5 h-5 border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" 
                    title="Tandai sebagai kunci jawaban yang benar">
                <span class="text-xs font-extrabold text-slate-700 w-5 text-center">${letter}</span>
            </div>
            <div class="flex-1">
                <input type="text" name="options[]" placeholder="Teks Pilihan ${letter}" required
                    class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
            </div>
        `;
        list.appendChild(row);
    }

    document.addEventListener('DOMContentLoaded', toggleOptions);
</script>
@endsection
