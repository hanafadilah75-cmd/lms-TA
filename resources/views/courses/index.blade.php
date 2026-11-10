@extends('layouts.app')

@section('page_title', 'My Courses')

@section('content')
<div class="space-y-6">
    <!-- Header with Create Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Materi Kursus Saya</h2>
            <p class="text-xs font-semibold text-slate-400 mt-0.5">Pilih modul materi kursus untuk meningkatkan pemahaman dan kompetensi Anda.</p>
        </div>
        @if(Auth::user()->isAdmin() || Auth::user()->isTutor())
            <a href="{{ route('courses.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-bold shadow-md transition duration-150">
                <span>➕</span>
                <span>Buat Kursus Baru</span>
            </a>
        @endif
    </div>

    <!-- Filter Bar (Matching Reference Photo: "Filter by: Time v | Level v | Type v | Language v") -->
    <form id="courses-filter-form" method="GET" action="{{ route('courses.index') }}" class="flex flex-wrap items-center gap-2.5 text-xs font-bold text-slate-500 py-1">
        <span class="text-slate-400 font-extrabold mr-1">Filter by:</span>

        <!-- Waktu / Time -->
        <div class="relative inline-flex items-center">
            <select name="sort" onchange="this.form.submit()" 
                class="appearance-none bg-slate-100/90 hover:bg-slate-200/80 text-slate-700 pl-3.5 pr-7 py-1.5 rounded-full text-xs font-bold border-none cursor-pointer focus:ring-2 focus:ring-[#534ebb]/30 transition">
                <option value="latest" {{ ($selectedSort ?? 'latest') === 'latest' ? 'selected' : '' }}>Waktu: Terbaru</option>
                <option value="oldest" {{ ($selectedSort ?? '') === 'oldest' ? 'selected' : '' }}>Waktu: Terlama</option>
                <option value="title_asc" {{ ($selectedSort ?? '') === 'title_asc' ? 'selected' : '' }}>Nama: A → Z</option>
                <option value="title_desc" {{ ($selectedSort ?? '') === 'title_desc' ? 'selected' : '' }}>Nama: Z → A</option>
            </select>
            <span class="pointer-events-none absolute right-2.5 text-[9px] text-slate-400">▼</span>
        </div>

        <!-- Level -->
        <div class="relative inline-flex items-center">
            <select name="level" onchange="this.form.submit()" 
                class="appearance-none bg-slate-100/90 hover:bg-slate-200/80 text-slate-700 pl-3.5 pr-7 py-1.5 rounded-full text-xs font-bold border-none cursor-pointer focus:ring-2 focus:ring-[#534ebb]/30 transition">
                <option value="all" {{ empty($selectedLevel) || $selectedLevel === 'all' ? 'selected' : '' }}>Level: Semua Level</option>
                <option value="beginner" {{ ($selectedLevel ?? '') === 'beginner' ? 'selected' : '' }}>Level: Beginner</option>
                <option value="intermediate" {{ ($selectedLevel ?? '') === 'intermediate' ? 'selected' : '' }}>Level: Intermediate</option>
                <option value="advanced" {{ ($selectedLevel ?? '') === 'advanced' ? 'selected' : '' }}>Level: Advanced</option>
            </select>
            <span class="pointer-events-none absolute right-2.5 text-[9px] text-slate-400">▼</span>
        </div>

        <!-- Type / Kategori -->
        <div class="relative inline-flex items-center">
            <select name="type" onchange="this.form.submit()" 
                class="appearance-none bg-slate-100/90 hover:bg-slate-200/80 text-slate-700 pl-3.5 pr-7 py-1.5 rounded-full text-xs font-bold border-none cursor-pointer focus:ring-2 focus:ring-[#534ebb]/30 transition">
                <option value="all" {{ empty($selectedType) || $selectedType === 'all' ? 'selected' : '' }}>Tipe: Semua Kategori</option>
                <option value="a" {{ ($selectedType ?? '') === 'a' ? 'selected' : '' }}>Tipe: Kursus A</option>
                <option value="b" {{ ($selectedType ?? '') === 'b' ? 'selected' : '' }}>Tipe: Kursus B</option>
            </select>
            <span class="pointer-events-none absolute right-2.5 text-[9px] text-slate-400">▼</span>
        </div>

        <!-- Bahasa -->
        <div class="relative inline-flex items-center">
            <span class="bg-slate-100/90 text-slate-700 px-3.5 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5">
                <span>Bahasa: Indonesia</span>
            </span>
        </div>

        @if(!empty($selectedLevel) || !empty($selectedType) || (!empty($selectedSort) && $selectedSort !== 'latest') || !empty($searchQuery))
            <a href="{{ route('courses.index') }}" class="text-xs font-bold text-rose-500 hover:text-rose-700 ml-1 flex items-center gap-1">
                <span>✕ Reset Filter</span>
            </a>
        @endif
    </form>

    <!-- Courses List matching Reference Photo Cards -->
    @php
        $cardStyles = [
            ['bg' => 'bg-white', 'border' => 'border-slate-100', 'btn' => 'bg-[#6b62d8] hover:bg-[#534ebb]', 'device_bg' => 'bg-[#f0edfd]'],
            ['bg' => 'bg-[#f5efff]', 'border' => 'border-purple-100', 'btn' => 'bg-[#a37cf0] hover:bg-[#8d61e6]', 'device_bg' => 'bg-[#ece4fc]'],
            ['bg' => 'bg-[#fff0f2]', 'border' => 'border-rose-100', 'btn' => 'bg-[#f47285] hover:bg-[#e15469]', 'device_bg' => 'bg-[#ffe4e7]'],
        ];
    @endphp

    <div class="space-y-5">
        @forelse($courses as $index => $course)
            @php
                $style = $cardStyles[$index % count($cardStyles)];
                $totalPagesCount = $course->modules->sum(fn($m) => max(1, (int) ($m->total_pages ?? 1)));
            @endphp
            <div class="{{ $style['bg'] }} rounded-3xl p-6 sm:p-8 border {{ $style['border'] }} shadow-sm hover:shadow-md transition-all duration-200 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <!-- Left & Middle: Tech Device Illustration + Course Info -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-6 flex-1 min-w-0">
                    <!-- Tech Device Mockup Box (Reference Photo Style) -->
                    <div class="w-32 h-24 sm:w-36 sm:h-28 rounded-2xl {{ $style['device_bg'] }} p-3 flex flex-col items-center justify-center relative shrink-0 overflow-hidden shadow-inner">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover rounded-xl">
                        @else
                            <!-- Sleek minimalist device illustration -->
                            <div class="w-16 h-12 rounded-lg bg-white shadow-sm border border-slate-200/60 flex items-center justify-center relative">
                                <span class="text-xl">💻</span>
                                <span class="absolute -bottom-1 -right-1 w-5 h-7 rounded-sm bg-slate-800 border border-white flex items-center justify-center text-[7px] text-white">📱</span>
                            </div>
                            <div class="flex gap-1.5 mt-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#534ebb]/40"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-[#534ebb]/20"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-[#534ebb]/60"></span>
                            </div>
                        @endif
                    </div>

                    <!-- Course Title & Description -->
                    <div class="space-y-1.5 flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-white/80 border border-slate-200/60 text-slate-700">
                                {{ $course->level }}
                            </span>
                            <span class="text-xs font-bold text-slate-400">
                                • {{ $course->modules_count }} Modul ({{ $totalPagesCount }} Halaman)
                            </span>
                        </div>

                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight leading-snug">
                            <a href="{{ route('courses.show', $course->id) }}" class="hover:text-[#534ebb] transition">
                                {{ $course->title }}
                            </a>
                        </h3>

                        <p class="text-xs font-semibold text-slate-500 line-clamp-2 leading-relaxed max-w-2xl">
                            {{ $course->description ?? 'Pelajari konsep inti dan praktik implementasi pada kurikulum pelatihan ini.' }}
                        </p>

                        <div class="pt-1 flex flex-wrap items-center gap-4 text-xs font-bold text-slate-400">
                            <span>Created by <strong class="text-slate-700 font-extrabold">{{ optional($course->tutor)->name ?? 'Tutor' }}</strong></span>
                            @if($course->companies && $course->companies->isNotEmpty())
                                <span>🏢 Klien: {{ $course->companies->pluck('name')->implode(', ') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Action Button: Circular Arrow Pill (Reference Photo Style) -->
                <div class="flex items-center justify-end md:justify-center shrink-0">
                    <a href="{{ route('courses.show', $course->id) }}" 
                        class="w-11 h-11 rounded-full {{ $style['btn'] }} text-white flex items-center justify-center shadow-md hover:scale-105 active:scale-95 transition-all duration-150"
                        title="Buka Materi Kursus">
                        <svg class="w-5 h-5 ml-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-100 text-slate-400 font-bold text-sm">
                Belum ada materi kursus yang tersedia untuk Anda saat ini.
            </div>
        @endforelse
    </div>

    @if($courses->hasPages())
        <div class="pt-4">
            {{ $courses->links() }}
        </div>
    @endif
</div>
@endsection
