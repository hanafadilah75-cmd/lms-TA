@extends('layouts.app')

@section('page_title', 'Tambah Pertanyaan')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
    <div class="border-b border-slate-100 pb-4 mb-6">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-[#534ebb]/10 text-[#534ebb] mb-2">
            Kuis: {{ $quiz->title }}
        </span>
        <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tambah Pertanyaan Baru</h3>
        <p class="text-xs font-semibold text-slate-400 mt-0.5">Tulis pertanyaan, opsi jawaban, atau kata kunci evaluasi AI.</p>
    </div>

    <form action="{{ route('quizzes.questions.store', $quiz->id) }}" method="POST" class="space-y-6">
        @csrf

        <!-- Question Text -->
        <div>
            <label for="question" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Kalimat Pertanyaan <span class="text-rose-500">*</span></label>
            <textarea name="question" id="question" required rows="4" placeholder="Ketik kalimat pertanyaan lengkap di sini..."
                class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition leading-relaxed">{{ old('question') }}</textarea>
            @error('question')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Question Type -->
        <div>
            <label for="type" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Tipe Pertanyaan <span class="text-rose-500">*</span></label>
            <select name="type" id="type" required onchange="toggleOptions()" class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                <option value="multiple_choice" {{ old('type', $quiz->type) === 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                <option value="essay" {{ old('type', $quiz->type) === 'essay' ? 'selected' : '' }}>Esai</option>
            </select>
            @error('type')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Options Container (For Multiple Choice) -->
        <div id="options-container" class="space-y-4 pt-4 border-t border-slate-100">
            <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Kustomisasi Pilihan Jawaban & Kunci</h4>
            <p class="text-[11px] font-semibold text-slate-400">Ketik opsi jawaban dan pilih radio button pada opsi yang menjadi <b>Kunci Jawaban yang Benar</b>.</p>
            
            @for($i = 0; $i < 4; $i++)
                <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    <!-- Correct option selection -->
                    <input type="radio" name="correct_option" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }}
                        class="w-5 h-5 border-slate-300 text-[#534ebb] focus:ring-[#534ebb] cursor-pointer" title="Jadikan Kunci Jawaban Benar">
                    
                    <div class="flex-1">
                        <input type="text" name="options[]" placeholder="Teks Pilihan {{ chr(65 + $i) }}" value="{{ old('options.'.$i) }}"
                            class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                    </div>
                </div>
            @endfor
            @error('options')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Ideal Answer Container (For Essay) -->
        <div id="ideal-answer-container" class="space-y-4 pt-4 border-t border-slate-100" style="display: none;">
            <div>
                <label for="ideal_answer" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">
                    🔑 Kunci Jawaban Acuan / Rubrik Penilaian Esai <span class="text-xs text-slate-400 font-semibold">(Opsional)</span>
                </label>
                <p class="text-[11px] font-semibold text-slate-400 mb-2">Tuliskan poin-poin kunci jawaban ideal yang harus ada dalam jawaban karyawan sebagai acuan AI dan Tutor.</p>
                <textarea name="ideal_answer" id="ideal_answer" rows="4" placeholder="Contoh: Jawaban wajib memuat konsep Active Record, fleksibilitas relasi model, efisiensi Query Builder..."
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition leading-relaxed font-semibold">{{ old('ideal_answer') }}</textarea>
                @error('ideal_answer')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="keywords" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">
                    🏷️ Kata Kunci Soal Esai (Dilempar ke Prompt AI) <span class="text-xs text-slate-400 font-semibold">(Opsional)</span>
                </label>
                <p class="text-[11px] font-semibold text-slate-400 mb-1.5">Kata kunci penting yang wajib dinilai AI dalam jawaban peserta (pisahkan dengan koma). AI akan mengevaluasi apakah kata kunci ini dipahami dan dijawab dengan tepat.</p>
                <input type="text" name="keywords" id="keywords" value="{{ old('keywords') }}" placeholder="Contoh: MVC, Controller, Model, Dependency Injection, Query Builder"
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition font-semibold">
                @error('keywords')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('courses.quizzes.show', [$quiz->course_id, $quiz->id]) }}" class="px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-xs font-extrabold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-xs font-extrabold text-white shadow-sm transition">
                Simpan Pertanyaan ➔
            </button>
        </div>
    </form>
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
    document.addEventListener('DOMContentLoaded', toggleOptions);
</script>
@endsection
