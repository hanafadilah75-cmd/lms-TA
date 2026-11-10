@extends('layouts.app')

@section('page_title', 'Dashboard Tutor')

@section('content')
<div class="space-y-8">
    <!-- Top Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <!-- Kursus Diampu -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-[#534ebb] flex items-center justify-center text-2xl font-bold shadow-inner">
                📚
            </div>
            <div>
                <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Kursus Ditugaskan</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-0.5">{{ $coursesCount }}</h3>
            </div>
        </div>

        <!-- Review Pending -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl font-bold shadow-inner">
                📝
            </div>
            <div>
                <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Esai Perlu Review</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-0.5">{{ $pendingReviewsCount }}</h3>
            </div>
        </div>

        <!-- Rata-rata Nilai -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold shadow-inner">
                🎯
            </div>
            <div>
                <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Rata-rata Nilai Kuis</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-0.5">{{ $avgScore }} <span class="text-sm text-slate-400 font-semibold">/ 100</span></h3>
            </div>
        </div>
    </div>

    <!-- Grouped Courses & Clients Section -->
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Kursus & Klien Yang Anda Kelola</h3>
                <p class="text-xs font-semibold text-slate-400 mt-0.5">Pantau materi pembelajaran dan progres halaman karyawan klien secara terperinci.</p>
            </div>
            <a href="{{ route('courses.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-bold shadow-md transition">
                <span>➕ Buat Kursus Baru</span>
            </a>
        </div>

        @if($courseData->isNotEmpty())
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @foreach($courseData as $row)
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <!-- Header: Course title & Company Badge -->
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div>
                                    <span class="inline-block px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-[#534ebb]/10 text-[#534ebb] mb-2">
                                        🏢 {{ $row['company']->name }}
                                    </span>
                                    <h4 class="text-lg font-extrabold text-slate-900 mt-0.5">
                                        <a href="{{ route('courses.show', $row['course']->id) }}" class="hover:text-[#534ebb] transition">
                                            {{ $row['course']->title }}
                                        </a>
                                    </h4>
                                    <p class="text-xs font-semibold text-slate-400 mt-1">
                                        Tutor: {{ optional($row['course']->tutor)->name ?? 'Tutor' }} • {{ $row['module_count'] }} Modul
                                    </p>
                                </div>

                                <!-- Status Badge -->
                                <div class="text-right shrink-0">
                                    @if($row['status'] === 'selesai')
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">
                                            ✓ 100% Selesai
                                        </span>
                                    @elseif($row['status'] === 'bervariasi')
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">
                                            Rata-rata {{ $row['average_progress'] }}%
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-500">
                                            0% Belum Mulai
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Page-Based Progress Bar -->
                            <div class="space-y-1.5 mb-5">
                                <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                                    <span class="text-slate-400">Progres Halaman Keseluruhan</span>
                                    <span class="font-extrabold text-slate-900">{{ $row['average_progress'] }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="h-full rounded-full bg-[#534ebb] transition-all duration-500" style="width: {{ $row['average_progress'] }}%"></div>
                                </div>
                            </div>

                            <!-- Employee Counts Summary -->
                            <div class="grid grid-cols-4 text-center py-3 bg-[#fcfbfe] rounded-2xl border border-slate-100 text-xs font-bold mb-4">
                                <div>
                                    <span class="block text-slate-400 text-[10px] uppercase">Peserta</span>
                                    <span class="text-slate-900 font-extrabold">{{ $row['employees_count'] }}</span>
                                </div>
                                <div>
                                    <span class="block text-emerald-600 text-[10px] uppercase">Selesai</span>
                                    <span class="text-emerald-700 font-extrabold">{{ $row['completed_count'] }}</span>
                                </div>
                                <div>
                                    <span class="block text-amber-600 text-[10px] uppercase">Belajar</span>
                                    <span class="text-amber-700 font-extrabold">{{ $row['in_progress_count'] }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 text-[10px] uppercase">Belum</span>
                                    <span class="text-slate-500 font-extrabold">{{ $row['not_started_count'] }}</span>
                                </div>
                            </div>

                            <!-- Expandable Employee Details by Pages -->
                            <details class="text-xs font-semibold text-slate-600 group">
                                <summary class="cursor-pointer text-[#534ebb] font-extrabold flex items-center justify-between py-2 border-t border-slate-100">
                                    <span>Lihat Rincian Progres Halaman Karyawan ({{ count($row['employee_details']) }})</span>
                                    <span class="text-sm group-open:rotate-180 transition-transform">▼</span>
                                </summary>
                                <div class="mt-3 space-y-2 max-h-48 overflow-y-auto pr-1">
                                    @foreach($row['employee_details'] as $emp)
                                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs font-bold">
                                            <div>
                                                <p class="text-slate-900 leading-tight">{{ $emp['name'] }}</p>
                                                <p class="text-[10px] font-semibold text-slate-400">📖 {{ $emp['pages_read'] ?? 0 }} / {{ $emp['total_pages'] ?? 1 }} Halaman</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="font-extrabold text-[#534ebb]">{{ $emp['percent'] }}%</span>
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase">{{ str_replace('_', ' ', $emp['status']) }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        </div>

                        <!-- Card Footer -->
                        <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('courses.show', $row['course']->id) }}" class="text-xs font-extrabold text-slate-700 hover:text-[#534ebb]">
                                Kelola Modul & Kuis ➔
                            </a>
                            <a href="{{ route('progress.company', $row['company']->id) }}" class="text-xs font-extrabold text-[#534ebb] hover:underline">
                                Monitoring Klien
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-100 text-slate-400 text-xs font-semibold">
                Belum ada kursus aktif yang ditugaskan kepada Anda.
            </div>
        @endif
    </div>
</div>
@endsection
