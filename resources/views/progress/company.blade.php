@extends('layouts.app')

@section('page_title', 'Progres Karyawan - ' . $company->name)

@section('content')
<div class="space-y-6">
    <div class="sm:flex sm:items-center justify-between">
        <div>
            <a href="{{ route('progress.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 mb-2 transition">
                <span>←</span>
                <span>Kembali ke Ikhtisar Perusahaan</span>
            </a>
            <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Daftar Progres Karyawan</h3>
            <p class="text-xs font-semibold text-slate-400 mt-0.5">Memantau kemajuan pelatihan karyawan di <b>{{ $company->name }}</b>.</p>
        </div>
    </div>

    <!-- Employees Progress Table Card -->
    <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-100 text-left">
                <thead class="bg-[#fcfbfe]">
                    <tr>
                        <th scope="col" class="py-4 pl-6 pr-3 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Karyawan</th>
                        <th scope="col" class="px-4 py-4 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Rata-rata Progres</th>
                        <th scope="col" class="px-4 py-4 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 w-32 shrink-0">Status</th>
                        <th scope="col" class="relative py-4 pl-3 pr-6 text-right text-[11px] font-extrabold uppercase tracking-wider text-slate-400 w-36 shrink-0 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($employees as $employee)
                        @php
                            $prog = $employeeProgressData[$employee->id] ?? ['average_progress' => 0, 'status' => 'belum_mulai'];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 pl-6 pr-3 text-sm">
                                <div class="font-extrabold text-slate-900 leading-tight">{{ $employee->name }}</div>
                                <div class="text-xs font-semibold text-slate-400 truncate mt-0.5">{{ $employee->email }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm max-w-xs">
                                <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                    <span class="text-slate-600">Penyelesaian</span>
                                    <span class="font-extrabold text-[#534ebb]">{{ $prog['average_progress'] }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden p-0.5">
                                    <div class="h-full rounded-full transition-all duration-300 {{ $prog['average_progress'] == 100 ? 'bg-emerald-500' : 'bg-[#534ebb]' }}" style="width: {{ $prog['average_progress'] }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm shrink-0 whitespace-nowrap">
                                @if($prog['status'] === 'selesai')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700">
                                        ✓ Selesai
                                    </span>
                                @elseif($prog['status'] === 'sedang_belajar')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-amber-50 text-amber-700">
                                        Sedang Belajar
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-600">
                                        Belum Mulai
                                    </span>
                                @endif
                            </td>
                            <td class="relative py-4 pl-3 pr-6 text-right text-sm font-medium shrink-0 whitespace-nowrap">
                                <a href="{{ route('progress.employee', $employee->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-extrabold shadow-sm transition">
                                    <span>Detail Progres</span>
                                    <span>→</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm font-bold text-slate-400">
                                Belum ada karyawan yang terdaftar di perusahaan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="p-4 border-t border-slate-100 bg-[#fcfbfe]">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
