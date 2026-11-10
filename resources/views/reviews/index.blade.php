@extends('layouts.app')

@section('page_title', 'Review Esai Karyawan')

@section('content')
<div class="sm:flex sm:items-center justify-between mb-8">
    <div>
        <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Review & Evaluasi Esai AI</h3>
        <p class="text-xs font-semibold text-slate-500 mt-0.5">Daftar jawaban esai karyawan yang membutuhkan pemeriksaan atau validasi skor oleh Tutor.</p>
    </div>
</div>

<!-- Filter Status & Kategori Kursus Switcher -->
<div class="space-y-4 mb-6">
    <!-- Status Switcher (Pending vs Finalized) -->
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
        <div class="flex items-center gap-2">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mr-1">Status:</span>
            <a href="{{ route('reviews.index', array_filter(['status' => 'pending', 'course_id' => $courseId])) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-extrabold transition-all {{ $status === 'pending' ? 'bg-[#534ebb] text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
                ⏳ Menunggu Review
            </a>
            <a href="{{ route('reviews.index', array_filter(['status' => 'finalized', 'course_id' => $courseId])) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-extrabold transition-all {{ $status === 'finalized' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
                ✓ Telah Direview
            </a>
        </div>
    </div>

    <!-- Kategori Kursus Switcher (Semua / Kursus A / Kursus B) -->
    @if(isset($courses) && $courses->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2 pt-1">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mr-1">Filter Kursus:</span>
            
            <!-- Tab: Semua Kursus -->
            <a href="{{ route('reviews.index', ['status' => $status]) }}" 
               class="px-4 py-2 rounded-2xl text-xs font-extrabold transition-all {{ empty($courseId) ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200' }}">
                📚 Semua Kursus
                <span class="ml-1.5 py-0.5 px-2 text-[10px] font-extrabold rounded-full {{ empty($courseId) ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                    {{ $totalCourseCount ?? 0 }}
                </span>
            </a>

            <!-- Tab: Kursus A, Kursus B, dst. -->
            @foreach($courses as $c)
                @php
                    $cCount = $courseCounts[$c->id] ?? 0;
                    $isActive = ($courseId == $c->id);
                @endphp
                <a href="{{ route('reviews.index', ['status' => $status, 'course_id' => $c->id]) }}" 
                   class="px-4 py-2 rounded-2xl text-xs font-extrabold transition-all {{ $isActive ? 'bg-[#534ebb] text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200' }}">
                    <span>📖 {{ $c->title }}</span>
                    <span class="ml-1.5 py-0.5 px-2 text-[10px] font-extrabold rounded-full {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $cCount }}
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</div>

<!-- Reviews Table Card -->
<div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full divide-y divide-slate-100 text-left">
            <thead class="bg-[#fcfbfe]">
                <tr>
                    <th scope="col" class="py-4 pl-6 pr-3 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Karyawan</th>
                    <th scope="col" class="px-4 py-4 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Materi / Kursus</th>
                    <th scope="col" class="px-4 py-4 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 text-center w-28 shrink-0">Skor AI</th>
                    @if($status !== 'pending')
                        <th scope="col" class="px-4 py-4 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 text-center w-36 shrink-0">Skor Final Tutor</th>
                    @endif
                    <th scope="col" class="relative py-4 pl-3 pr-6 text-right text-[11px] font-extrabold uppercase tracking-wider text-slate-400 w-36 shrink-0 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($reviews as $review)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-4 pl-6 pr-3 text-sm">
                            <div class="font-extrabold text-slate-900 leading-tight">{{ $review->user?->name ?? 'Karyawan Tidak Aktif' }}</div>
                            <div class="text-xs font-semibold text-slate-400 mt-0.5 truncate max-w-[200px]">🏢 {{ $review->user?->company?->name ?? 'Perusahaan Tidak Aktif' }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-500 max-w-xs">
                            <div class="font-bold text-slate-800 line-clamp-1">{{ $review->question->quiz->course->title }}</div>
                            <div class="text-xs font-semibold text-slate-400 mt-0.5 line-clamp-1">{{ $review->question->quiz->title }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm font-extrabold text-slate-900 text-center shrink-0 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-[#534ebb]/10 text-[#534ebb]">
                                {{ $review->ai_score }} / 100
                            </span>
                        </td>
                        @if($status !== 'pending')
                            <td class="px-4 py-4 text-sm font-extrabold text-emerald-600 text-center shrink-0 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700">
                                    {{ $review->final_score }} / 100
                                </span>
                            </td>
                        @endif
                        <td class="relative py-4 pl-3 pr-6 text-right text-sm font-medium shrink-0 whitespace-nowrap">
                            <a href="{{ route('reviews.show', $review->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl {{ $status === 'pending' ? 'bg-[#534ebb] hover:bg-[#433ea1] text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} text-xs font-extrabold shadow-sm transition">
                                <span>{{ $status === 'pending' ? 'Review & Nilai' : 'Lihat Detail' }}</span>
                                <span>→</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $status !== 'pending' ? 5 : 4 }}" class="px-6 py-12 text-center text-sm font-bold text-slate-400">
                            Tidak ada esai dalam antrean ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reviews->hasPages())
        <div class="p-4 border-t border-slate-100 bg-[#fcfbfe]">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
@endsection
