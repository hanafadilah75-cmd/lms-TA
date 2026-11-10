@extends('layouts.app')

@section('page_title', 'Edit Kursus')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Data Kursus</h3>
        <p class="text-xs font-semibold text-slate-400 mt-0.5">Perbarui informasi kursus {{ $course->title }}.</p>
    </div>

    <form action="{{ route('courses.update', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Title Input -->
        <div>
            <label for="title" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Judul Kursus <span class="text-rose-500">*</span></label>
            <input type="text" name="title" id="title" required value="{{ old('title', $course->title) }}"
                class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
            @error('title')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description Input -->
        <div>
            <label for="description" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Deskripsi Kursus</label>
            <textarea name="description" id="description" rows="4"
                class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">{{ old('description', $course->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Level & Tutor Assignment -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label for="level" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Level Kesulitan <span class="text-rose-500">*</span></label>
                <select name="level" id="level" required class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                    <option value="beginner" {{ old('level', $course->level) === 'beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="intermediate" {{ old('level', $course->level) === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="advanced" {{ old('level', $course->level) === 'advanced' ? 'selected' : '' }}>Advanced</option>
                </select>
                @error('level')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                @if(Auth::user()->isAdmin())
                    <label for="tutor_id" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Tugaskan Tutor <span class="text-rose-500">*</span></label>
                    <select name="tutor_id" id="tutor_id" required class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                        <option value="">Pilih Tutor...</option>
                        @foreach($tutors as $tutor)
                            <option value="{{ $tutor->id }}" {{ old('tutor_id', $course->tutor_id) == $tutor->id ? 'selected' : '' }}>{{ $tutor->name }}</option>
                        @endforeach
                    </select>
                    @error('tutor_id')
                        <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                    @enderror
                @else
                    <label class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Tutor</label>
                    <input type="text" disabled value="{{ optional($course->tutor)->name ?? 'Tutor' }}"
                        class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-500 bg-slate-50 cursor-not-allowed">
                    <input type="hidden" name="tutor_id" value="{{ $course->tutor_id ?? Auth::id() }}">
                @endif
            </div>
        </div>

        <!-- Multi-select Client Companies -->
        <div>
            <label class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">
                Tugaskan ke Perusahaan Klien
            </label>
            <p class="text-[11px] font-semibold text-slate-400 mb-3">Centang perusahaan klien yang dapat mengakses materi ini.</p>
            <div class="space-y-2 max-h-48 overflow-y-auto p-4 rounded-2xl border border-slate-100 bg-[#fcfbfe]">
                @php
                    $selectedCompanyIds = old('companies', $course->companies->pluck('id')->toArray());
                @endphp
                @forelse($companies as $company)
                    <label class="flex items-center gap-3 p-2.5 rounded-xl bg-white border border-slate-100 hover:bg-slate-50 cursor-pointer font-bold text-xs text-slate-800 transition">
                        <input type="checkbox" name="companies[]" value="{{ $company->id }}"
                            {{ in_array($company->id, $selectedCompanyIds) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-slate-300 text-[#534ebb] focus:ring-[#534ebb]">
                        <span>🏢 {{ $company->name }}</span>
                    </label>
                @empty
                    <p class="text-xs font-bold text-slate-400">Belum ada perusahaan klien yang terdaftar.</p>
                @endforelse
            </div>
            @error('companies')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Thumbnail Upload -->
        <div>
            <label for="thumbnail" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-2">Thumbnail Banner Kursus</label>
            <div class="flex items-center gap-4 mb-2">
                @if($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Thumbnail" class="w-16 h-12 object-cover rounded-xl border border-slate-200">
                @endif
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                    class="text-xs font-bold text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-extrabold file:bg-[#534ebb]/10 file:text-[#534ebb] hover:file:bg-[#534ebb]/20 cursor-pointer">
            </div>
            @error('thumbnail')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
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
