@extends('layouts.app')

@section('page_title', 'Edit Kuis')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
    <div class="border-b border-slate-100 pb-4 mb-6">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-[#534ebb]/10 text-[#534ebb] mb-2">
            Kursus: {{ $course->title }}
        </span>
        <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Kuis Evaluasi</h3>
        <p class="text-xs font-semibold text-slate-400 mt-0.5">Perbarui pengaturan kuis {{ $quiz->title }}.</p>
    </div>

    <form action="{{ route('quizzes.update', $quiz->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div>
            <label for="title" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Judul Kuis <span class="text-rose-500">*</span></label>
            <input type="text" name="title" id="title" required value="{{ old('title', $quiz->title) }}"
                class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
            @error('title')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Quiz Type & Duration & Passing Grade -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <div>
                <label for="type" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Tipe Pertanyaan <span class="text-rose-500">*</span></label>
                <select name="type" id="type" required class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                    <option value="multiple_choice" {{ old('type', $quiz->type) === 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                    <option value="essay" {{ old('type', $quiz->type) === 'essay' ? 'selected' : '' }}>Esai</option>
                </select>
                @error('type')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="duration" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Durasi (Menit) <span class="text-rose-500">*</span></label>
                <input type="number" name="duration" id="duration" required min="1" value="{{ old('duration', $quiz->duration) }}"
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                @error('duration')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="passing_grade" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Passing Grade (%) <span class="text-rose-500">*</span></label>
                <input type="number" name="passing_grade" id="passing_grade" required min="0" max="100" value="{{ old('passing_grade', $quiz->passing_grade) }}"
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                @error('passing_grade')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('courses.show', $course->id) }}" class="px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-xs font-extrabold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-xs font-extrabold text-white shadow-sm transition">
                Simpan Perubahan ➔
            </button>
        </div>
    </form>
</div>
@endsection
