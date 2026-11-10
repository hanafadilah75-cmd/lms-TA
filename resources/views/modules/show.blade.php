@extends('layouts.app')

@section('page_title', 'Materi Modul')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb / Back button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('courses.show', $course->id) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-[#534ebb] transition">
            <span>← Kembali ke Kursus:</span>
            <span class="text-slate-800 font-extrabold">{{ $course->title }}</span>
        </a>

        @if(Auth::user()->isAdmin() || Auth::user()->isTutor())
            <a href="{{ route('modules.edit', $module->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition">
                <span>✏️ Edit Modul</span>
            </a>
        @endif
    </div>

    <!-- Main Module Card -->
    <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        
        <!-- Module Header -->
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-[#fcfbfe] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-3 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-[#534ebb]/10 text-[#534ebb]">
                        Modul #{{ $module->order_number }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-600">
                        Total {{ $totalPages }} Halaman
                    </span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">{{ $module->title }}</h2>
            </div>

            <!-- Page Reading Progress Pill for Employee -->
            @if(Auth::user()->isKaryawan())
                <div class="sm:text-right shrink-0 bg-white px-4 py-2.5 rounded-2xl border border-slate-100 shadow-sm">
                    <div class="text-xs font-extrabold text-slate-900">
                        Halaman {{ $currentPage }} dari {{ $totalPages }}
                    </div>
                    <div class="w-32 bg-slate-100 rounded-full h-2 mt-1.5 overflow-hidden">
                        <div class="h-full rounded-full bg-[#534ebb] transition-all duration-300" style="width: {{ $modulePagePercent }}%"></div>
                    </div>
                    <span class="text-[10px] font-bold text-[#534ebb] block mt-0.5">{{ $modulePagePercent }}% Modul Dipelajari</span>
                </div>
            @endif
        </div>

        <!-- Video Player (If YouTube URL is available) -->
        @if($embedUrl)
            <div class="p-6 sm:p-8 bg-slate-50 border-b border-slate-100">
                <div class="aspect-video w-full rounded-2xl overflow-hidden shadow-md">
                    <iframe class="w-full h-full" src="{{ $embedUrl }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <div class="p-6 sm:p-8 min-h-[300px]">
            <!-- Page Number Tag if multi-page -->
            @if($totalPages > 1)
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100 text-xs text-slate-400 font-semibold">
                    <span>Bagian Halaman {{ $currentPage }} dari {{ $totalPages }}</span>
                    <span>Modul Pelatihan E-Training</span>
                </div>
            @endif

            <div class="prose prose-slate max-w-none text-slate-800 leading-relaxed font-medium">
                {!! $displayContent !!}
            </div>
        </div>

        <!-- Page Navigator Toolbar (For multi-page modules) -->
        @if($totalPages > 1)
            <div class="p-4 sm:p-6 bg-[#fcfbfe] border-t border-slate-100 flex items-center justify-between gap-4">
                @if($currentPage > 1)
                    <a href="{{ route('modules.show', ['course' => $course->id, 'module' => $module->id, 'page' => $currentPage - 1]) }}"
                       class="px-4 py-2 rounded-full border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                        ← Halaman {{ $currentPage - 1 }}
                    </a>
                @else
                    <div></div>
                @endif

                <div class="flex items-center gap-1.5">
                    @for($p = 1; $p <= $totalPages; $p++)
                        <a href="{{ route('modules.show', ['course' => $course->id, 'module' => $module->id, 'page' => $p]) }}"
                           class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition {{ $p == $currentPage ? 'bg-[#534ebb] text-white shadow-sm' : ($p <= $highestRead ? 'bg-[#534ebb]/15 text-[#534ebb]' : 'bg-slate-100 text-slate-400 hover:bg-slate-200') }}">
                            {{ $p }}
                        </a>
                    @endfor
                </div>

                @if($currentPage < $totalPages)
                    <a href="{{ route('modules.show', ['course' => $course->id, 'module' => $module->id, 'page' => $currentPage + 1]) }}"
                       class="px-4 py-2 rounded-full bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-bold shadow-sm transition">
                        Lanjut ke Hal. {{ $currentPage + 1 }} →
                    </a>
                @else
                    <div></div>
                @endif
            </div>
        @endif

        <!-- Employee Completion Action Footer -->
        @if(Auth::user()->isKaryawan())
            <div class="p-6 sm:p-8 bg-slate-50/70 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    @if($isCompleted)
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold">
                            ✓ Seluruh {{ $totalPages }} halaman modul ini telah Anda selesaikan!
                        </span>
                    @else
                        <p class="text-xs font-bold text-slate-500">
                            Halaman yang Anda buka tercatat otomatis ke dalam kalkulasi progres belajar Anda.
                        </p>
                    @endif
                </div>

                @if(!$isCompleted)
                    <form action="{{ route('modules.complete', $module->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-2.5 rounded-full bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-extrabold shadow-md transition">
                            Tandai Modul Selesai & Lanjut ➔
                        </button>
                    </form>
                @endif
            </div>
        @endif

        <!-- Adjacent Modules Navigation Footer -->
        <div class="p-6 sm:p-8 border-t border-slate-100 flex items-center justify-between gap-4 bg-white">
            @if($previousModule)
                <a href="{{ route('modules.show', [$course->id, $previousModule->id]) }}" class="px-4 py-2 rounded-full border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    ← Modul #{{ $previousModule->order_number }}: {{ Str::limit($previousModule->title, 25) }}
                </a>
            @else
                <div></div>
            @endif

            @if($nextModule)
                <a href="{{ route('modules.show', [$course->id, $nextModule->id]) }}" class="px-5 py-2.5 rounded-full bg-[#534ebb] hover:bg-[#433ea3] text-xs font-bold text-white shadow-sm transition">
                    Modul Berikutnya: {{ Str::limit($nextModule->title, 25) }} ➔
                </a>
            @else
                <a href="{{ route('courses.show', $course->id) }}" class="px-5 py-2.5 rounded-full bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white shadow-sm transition">
                    Selesai & Lihat Halaman Kursus ➔
                </a>
            @endif
        </div>

    </div>
</div>
@endsection
