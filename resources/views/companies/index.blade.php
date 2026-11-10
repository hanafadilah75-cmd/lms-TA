@extends('layouts.app')

@section('page_title', 'Kelola Perusahaan')

@section('content')
<div class="space-y-6">
    <div class="sm:flex sm:items-center justify-between">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Direktori Perusahaan</h3>
            <p class="text-xs font-semibold text-slate-400 mt-0.5">Daftar entitas perusahaan yang terdaftar dalam program pelatihan e-training.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('companies.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-bold shadow-md transition">
                <span>➕</span>
                <span>Tambah Perusahaan</span>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-100 text-left">
                <thead class="bg-[#fcfbfe]">
                    <tr>
                        <th scope="col" class="py-4 pl-6 pr-3 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Nama Perusahaan</th>
                        <th scope="col" class="px-4 py-4 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Alamat</th>
                        <th scope="col" class="px-4 py-4 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Kontak</th>
                        <th scope="col" class="px-4 py-4 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 text-center shrink-0 w-28">Karyawan</th>
                        <th scope="col" class="relative py-4 pl-3 pr-6 text-right text-[11px] font-extrabold uppercase tracking-wider text-slate-400 shrink-0 whitespace-nowrap w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($companies as $company)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 pl-6 pr-3 text-sm font-bold text-slate-800">
                                <div class="font-extrabold text-slate-900 leading-snug">{{ $company->name }}</div>
                                @if($company->type === 'startup')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#534ebb]/10 text-[#534ebb] mt-1">
                                        🚀 Startup Penyedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 mt-1">
                                        🏢 Perusahaan Klien
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-xs font-semibold text-slate-500 max-w-[200px]">
                                <div class="line-clamp-2 leading-relaxed" title="{{ $company->address }}">
                                    {{ $company->address ?? '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 text-xs text-slate-600 font-semibold">
                                <div class="truncate max-w-[180px]">{{ $company->email ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $company->phone ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm font-semibold text-center shrink-0 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-600">
                                    👥 {{ $company->users_count }} Karyawan
                                </span>
                            </td>
                            <td class="relative py-4 pl-3 pr-6 text-right text-sm font-medium shrink-0 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('companies.edit', $company->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-extrabold text-slate-700 transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('companies.destroy', $company->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus perusahaan ini?')">
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
                            <td colspan="5" class="px-6 py-12 text-center text-sm font-bold text-slate-400">
                                Belum ada data perusahaan yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($companies->hasPages())
            <div class="p-4 border-t border-slate-100 bg-[#fcfbfe]">
                {{ $companies->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
