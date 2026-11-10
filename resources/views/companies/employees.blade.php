@extends('layouts.app')

@section('page_title', 'Kelola Karyawan & Tutor')

@section('content')
<div class="space-y-6">
    <div class="sm:flex sm:items-center justify-between">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Pengguna</h3>
            <p class="text-xs font-semibold text-slate-400 mt-0.5">Daftar pengguna terdaftar yang berpartisipasi sebagai karyawan pelatihan atau tutor materi.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('companies.employees.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-bold shadow-md transition">
                <span>➕</span>
                <span>Tambah User Baru</span>
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
        <form action="{{ route('companies.employees') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
            <div>
                <label for="search" class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Cari Nama / Email</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ketik nama atau email..."
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
            </div>

            <div>
                <label for="role" class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Role</label>
                <select name="role" id="role" class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                    <option value="">Semua Role</option>
                    <option value="karyawan" {{ request('role') === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                    <option value="tutor" {{ request('role') === 'tutor' ? 'selected' : '' }}>Tutor</option>
                </select>
            </div>

            <div>
                <label for="company_id" class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Perusahaan</label>
                <select name="company_id" id="company_id" class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                    <option value="">Semua Perusahaan</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-extrabold shadow-sm transition">
                    Filter ➔
                </button>
                <a href="{{ route('companies.employees') }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Employees Table Card -->
    <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-100 text-left">
                <thead class="bg-[#fcfbfe]">
                    <tr>
                        <th scope="col" class="py-4 pl-6 pr-3 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Nama Pengguna</th>
                        <th scope="col" class="px-4 py-4 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 w-28 shrink-0">Role</th>
                        <th scope="col" class="px-4 py-4 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Perusahaan</th>
                        <th scope="col" class="relative py-4 pl-3 pr-6 text-right text-[11px] font-extrabold uppercase tracking-wider text-slate-400 w-44 shrink-0 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 pl-6 pr-3 text-sm">
                                <div class="flex items-center gap-3 min-w-0">
                                    @if($employee->avatar)
                                        <img class="h-10 w-10 rounded-2xl object-cover border border-slate-100 shrink-0" src="{{ asset('storage/' . $employee->avatar) }}" alt="">
                                    @else
                                        <div class="h-10 w-10 rounded-2xl bg-[#534ebb]/10 text-[#534ebb] flex items-center justify-center font-extrabold text-sm uppercase shrink-0">
                                            {{ substr($employee->name, 0, 2) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-slate-900 leading-tight truncate">{{ $employee->name }}</div>
                                        <div class="text-xs font-semibold text-slate-400 truncate">{{ $employee->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm shrink-0 whitespace-nowrap">
                                @if($employee->role === 'tutor')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-[#534ebb]/10 text-[#534ebb]">
                                        🎓 Tutor
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700">
                                        👤 Karyawan
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-xs font-bold text-slate-600 max-w-[220px]">
                                <span class="line-clamp-2 leading-snug">{{ optional($employee->company)->name ?? '-' }}</span>
                            </td>
                            <td class="relative py-4 pl-3 pr-6 text-right text-sm font-medium shrink-0 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($employee->role === 'karyawan')
                                        <a href="{{ route('progress.employee', $employee->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-extrabold text-slate-700 transition">
                                            Rapor
                                        </a>
                                    @endif
                                    <a href="{{ route('companies.employees.edit', $employee->id) }}" class="px-3 py-1.5 rounded-xl bg-[#534ebb]/10 hover:bg-[#534ebb]/20 text-xs font-extrabold text-[#534ebb] transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('companies.employees.destroy', $employee->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-xs font-extrabold text-rose-700 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm font-bold text-slate-400">
                                Tidak ada pengguna ditemukan.
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
