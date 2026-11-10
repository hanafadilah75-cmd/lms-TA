@extends('layouts.app')

@section('page_title', 'Edit Modul')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
    <div class="border-b border-slate-100 pb-4 mb-6">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-[#534ebb]/10 text-[#534ebb] mb-2">
            Kursus: {{ $course->title }}
        </span>
        <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Modul Materi</h3>
        <p class="text-xs font-semibold text-slate-400 mt-0.5">Perbarui informasi modul #{{ $module->order_number }}.</p>
    </div>

    <form action="{{ route('modules.update', $module->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div>
            <label for="title" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Judul Modul <span class="text-rose-500">*</span></label>
            <input type="text" name="title" id="title" required value="{{ old('title', $module->title) }}"
                class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
            @error('title')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Video URL, Order Number & Total Pages -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
            <div class="sm:col-span-2">
                <label for="video_url" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">URL Video YouTube (Opsional)</label>
                <input type="url" name="video_url" id="video_url" value="{{ old('video_url', $module->video_url) }}"
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                @error('video_url')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="order_number" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Urutan <span class="text-rose-500">*</span></label>
                <input type="number" name="order_number" id="order_number" required min="1" value="{{ old('order_number', $module->order_number) }}"
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                @error('order_number')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="total_pages" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Jumlah Halaman <span class="text-rose-500">*</span></label>
                <input type="number" name="total_pages" id="total_pages" required min="1" max="100" value="{{ old('total_pages', $module->total_pages ?? 1) }}"
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                @error('total_pages')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Content Rich Text -->
        <div>
            <label for="content" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">Isi Materi Modul <span class="text-rose-500">*</span></label>
            <p class="text-[11px] font-semibold text-slate-400 mb-2">Anda dapat memisahkan per halaman dengan penanda <code>&lt;!-- pagebreak --&gt;</code>.</p>
            <textarea name="content" id="content" required rows="10"
                class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition leading-relaxed">{{ old('content', $module->content) }}</textarea>
            @error('content')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('courses.show', $course->id) }}" class="px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-xs font-extrabold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-xs font-extrabold text-white shadow-sm transition">
                Simpan Perubahan Modul ➔
            </button>
        </div>
    </form>
</div>
@endsection
