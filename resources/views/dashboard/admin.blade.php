@extends('layouts.app')

@section('page_title', 'Dashboard Admin')

@section('content')
<div class="space-y-8">
    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Perusahaan Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-[#534ebb] flex items-center justify-center text-2xl font-bold shadow-inner">
                🏢
            </div>
            <div>
                <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Perusahaan Klien</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-0.5">{{ $totalCompanies }}</h3>
            </div>
        </div>

        <!-- Total Karyawan Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl font-bold shadow-inner">
                👥
            </div>
            <div>
                <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Total Karyawan</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-0.5">{{ $totalEmployees }}</h3>
            </div>
        </div>

        <!-- Total Kursus Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-2xl font-bold shadow-inner">
                📚
            </div>
            <div>
                <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Materi Kursus</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-0.5">{{ $totalCourses }}</h3>
            </div>
        </div>

        <!-- Completion Rate Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold shadow-inner">
                📈
            </div>
            <div>
                <p class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Rata-rata Kelulusan</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-0.5">{{ $avgCompletionRate }}%</h3>
            </div>
        </div>
    </div>

    <!-- Progress Chart & Quick Actions Section -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Chart Container -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Grafik Penyelesaian Pelatihan</h3>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Persentase rata-rata kemajuan materi per perusahaan klien</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#534ebb]/10 text-[#534ebb]">Statistik Real-time</span>
            </div>
            
            @if(count($chartData) > 0)
                <div class="space-y-4 pt-2">
                    @foreach($chartData as $data)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs font-bold">
                                <span class="text-slate-800">{{ $data['name'] }}</span>
                                <span class="text-[#534ebb] font-extrabold">{{ $data['rate'] }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                                <div class="h-full rounded-full bg-[#534ebb] transition-all duration-700" style="width: {{ $data['rate'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-slate-400 font-semibold text-xs">
                    Belum ada data perusahaan klien untuk ditampilkan.
                </div>
            @endif
        </div>

        <!-- Quick Navigation Panel -->
        <div class="bg-[#fcfbfe] rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between space-y-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 tracking-tight mb-1">Aksi Cepat Admin</h3>
                <p class="text-xs text-slate-400 font-semibold mb-4">Akses langsung fitur utama sistem pelatihan</p>

                <div class="space-y-2.5">
                    <a href="{{ route('courses.create') }}" class="flex items-center justify-between p-3.5 bg-white rounded-2xl border border-slate-100 hover:border-slate-200 text-xs font-bold text-slate-800 transition">
                        <span>➕ Buat Kursus Baru</span>
                        <span>➔</span>
                    </a>
                    <a href="{{ route('companies.employees.create') }}" class="flex items-center justify-between p-3.5 bg-white rounded-2xl border border-slate-100 hover:border-slate-200 text-xs font-bold text-slate-800 transition">
                        <span>👤 Tambah Karyawan / Tutor</span>
                        <span>➔</span>
                    </a>
                    <a href="{{ route('companies.create') }}" class="flex items-center justify-between p-3.5 bg-white rounded-2xl border border-slate-100 hover:border-slate-200 text-xs font-bold text-slate-800 transition">
                        <span>🏢 Tambah Perusahaan Klien</span>
                        <span>➔</span>
                    </a>
                    <a href="{{ route('activities.index') }}" class="flex items-center justify-between p-3.5 bg-white rounded-2xl border border-slate-100 hover:border-slate-200 text-xs font-bold text-[#534ebb] transition">
                        <span>📜 Buka Log Aktivitas Lengkap</span>
                        <span>➔</span>
                    </a>
                </div>
            </div>

            <div class="p-3 bg-white rounded-2xl border border-slate-100 text-[11px] font-bold text-slate-500">
                💡 <span class="text-slate-800 font-extrabold">Tips:</span> Penilaian esai AI otomatis menggunakan kustomisasi kata kunci dan rubrik jawaban.
            </div>
        </div>
    </div>

    <!-- Grouped Courses & Clients Section -->
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Status Penugasan Kursus per Perusahaan Klien</h3>
                <p class="text-xs font-semibold text-slate-400 mt-0.5">Pemantauan progres pembelajaran berbasis halaman per kursus dan klien.</p>
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

                            <!-- Metrics Counters -->
                            <div class="grid grid-cols-3 gap-3 text-center text-xs mb-4">
                                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                    <span class="block text-lg font-extrabold text-slate-900">{{ $row['employees_count'] }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Peserta</span>
                                </div>
                                <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-100">
                                    <span class="block text-lg font-extrabold text-emerald-700">{{ $row['completed_count'] }}</span>
                                    <span class="text-[10px] font-bold text-emerald-600 uppercase">Selesai 100%</span>
                                </div>
                                <div class="p-3 rounded-2xl bg-amber-50 border border-amber-100">
                                    <span class="block text-lg font-extrabold text-amber-700">{{ $row['in_progress_count'] }}</span>
                                    <span class="text-[10px] font-bold text-amber-600 uppercase">Sedang Belajar</span>
                                </div>
                            </div>

                            <!-- Individual Breakdown -->
                            @if(count($row['employee_details']) > 0)
                                <details class="group rounded-2xl border border-slate-100 overflow-hidden text-xs">
                                    <summary class="flex items-center justify-between p-3 bg-slate-50 cursor-pointer font-bold text-slate-800 select-none">
                                        <span>🔍 Rincian Progres Individual ({{ count($row['employee_details']) }} Peserta)</span>
                                        <span class="transition group-open:rotate-180">▼</span>
                                    </summary>
                                    <div class="divide-y divide-slate-100 p-2 bg-white max-h-48 overflow-y-auto">
                                        @foreach($row['employee_details'] as $emp)
                                            <div class="flex items-center justify-between py-2 px-2 text-xs font-bold">
                                                <div>
                                                    <div class="font-extrabold text-slate-900">{{ $emp['name'] }}</div>
                                                    <div class="text-[10px] font-semibold text-slate-400">📖 {{ $emp['pages_read'] ?? 0 }} / {{ $emp['total_pages'] ?? 1 }} Halaman</div>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <div class="w-16 bg-slate-100 rounded-full h-2 overflow-hidden">
                                                        <div class="h-full rounded-full bg-[#534ebb]" style="width: {{ $emp['percent'] }}%"></div>
                                                    </div>
                                                    <span class="font-extrabold text-xs text-[#534ebb]">{{ $emp['percent'] }}%</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            @endif
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
                Belum ada kursus yang ditugaskan ke perusahaan klien.
            </div>
        @endif
    </div>
</div>
@endsection
