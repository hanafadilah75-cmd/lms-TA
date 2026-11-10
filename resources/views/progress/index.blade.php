@extends('layouts.app')

@section('page_title', 'Pemantauan Progres Belajar')

@section('content')
<div class="space-y-6">
    <div class="sm:flex sm:items-center justify-between">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Progres Pembelajaran per Perusahaan</h3>
            <p class="text-xs font-semibold text-slate-400 mt-0.5">Ikhtisar kemajuan pelatihan dan materi karyawan di setiap entitas klien.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        @forelse($companyStats as $stat)
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <!-- Company Header -->
                    <div class="flex items-center justify-between mb-5 pb-4 border-b border-slate-100">
                        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 flex items-center gap-2">
                            <span>🏢</span>
                            <span>{{ $stat['company']->name }}</span>
                        </h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-[#534ebb]/10 text-[#534ebb]">
                            👥 {{ $stat['employee_count'] }} Karyawan
                        </span>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Progress Bar -->
                        <div>
                            <div class="flex items-center justify-between text-xs font-bold text-slate-600 mb-2">
                                <span>Rata-rata Kemajuan:</span>
                                <span class="font-extrabold text-[#534ebb] text-sm">{{ $stat['avg_completion_rate'] }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5">
                                <div class="h-full rounded-full transition-all duration-500 {{ $stat['avg_completion_rate'] == 100 ? 'bg-emerald-500' : 'bg-[#534ebb]' }}" style="width: {{ $stat['avg_completion_rate'] }}%"></div>
                            </div>
                        </div>

                        <!-- Status Distribution -->
                        <div class="grid grid-cols-3 gap-2.5 pt-2 text-center text-xs">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="font-extrabold text-slate-800 text-lg">{{ $stat['not_started_count'] }}</div>
                                <div class="text-[10px] text-slate-400 font-extrabold uppercase mt-0.5">Belum Mulai</div>
                            </div>
                            <div class="p-3 rounded-2xl bg-amber-50/80 border border-amber-100/80">
                                <div class="font-extrabold text-amber-700 text-lg">{{ $stat['in_progress_count'] }}</div>
                                <div class="text-[10px] text-amber-600 font-extrabold uppercase mt-0.5">Belajar</div>
                            </div>
                            <div class="p-3 rounded-2xl bg-emerald-50/80 border border-emerald-100/80">
                                <div class="font-extrabold text-emerald-700 text-lg">{{ $stat['completed_count'] }}</div>
                                <div class="text-[10px] text-emerald-600 font-extrabold uppercase mt-0.5">Selesai</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Button -->
                <div class="pt-6 mt-6 border-t border-slate-100">
                    <a href="{{ route('progress.company', $stat['company']->id) }}" 
                       class="w-full py-3 px-4 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-extrabold flex items-center justify-center gap-2 shadow-sm transition">
                        <span>Lihat Progres Karyawan</span>
                        <span>→</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-slate-100 text-slate-400 font-bold text-sm">
                Belum ada perusahaan yang terdaftar.
            </div>
        @endforelse
    </div>
</div>
@endsection
