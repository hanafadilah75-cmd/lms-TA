@extends('layouts.app')

@section('page_title', 'Edit Perusahaan')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Data Perusahaan</h3>
        <p class="text-xs font-semibold text-slate-400 mt-0.5">Perbarui informasi perusahaan {{ $company->name }}.</p>
    </div>

    <form action="{{ route('companies.update', $company->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Name Input -->
        <div>
            <label for="name" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Nama Perusahaan <span class="text-rose-500">*</span></label>
            <input type="text" name="name" id="name" required value="{{ old('name', $company->name) }}"
                class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
            @error('name')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Entity Type Selector -->
        <div>
            <label for="type" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Kategori Entitas <span class="text-rose-500">*</span></label>
            <select name="type" id="type" required class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                <option value="client" {{ old('type', $company->type) === 'client' ? 'selected' : '' }}>🏢 Perusahaan Klien (Pengguna Kursus / Karyawan)</option>
                <option value="startup" {{ old('type', $company->type) === 'startup' ? 'selected' : '' }}>🚀 Pihak Startup (Penyedia Platform / Tutor)</option>
            </select>
            @error('type')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Address Input -->
        <div>
            <label for="address" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Alamat Perusahaan</label>
            <textarea name="address" id="address" rows="3"
                class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">{{ old('address', $company->address) }}</textarea>
            @error('address')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Two Columns: Email & Phone -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label for="email" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Email Perusahaan</label>
                <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}"
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                @error('email')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Nomor Telepon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone) }}"
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                @error('phone')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('companies.index') }}" class="px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-xs font-extrabold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-xs font-extrabold text-white shadow-sm transition">
                Simpan Perubahan ➔
            </button>
        </div>
    </form>
</div>
@endsection
