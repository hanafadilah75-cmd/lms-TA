@extends('layouts.app')

@section('page_title', 'Tambah User Baru')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tambah Pengguna Baru</h3>
        <p class="text-xs font-semibold text-slate-400 mt-0.5">Daftarkan akun tutor materi atau karyawan peserta pelatihan.</p>
    </div>

    <form action="{{ route('companies.employees.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Name Input -->
        <div>
            <label for="name" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
            <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Nama pengguna..."
                class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
            @error('name')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email & Password -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label for="email" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Alamat Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}" placeholder="email@perusahaan.com"
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                @error('email')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Kata Sandi <span class="text-rose-500">*</span></label>
                <input type="password" name="password" id="password" required placeholder="Minimal 8 karakter"
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                @error('password')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Role & Company -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label for="role" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Role <span class="text-rose-500">*</span></label>
                <select name="role" id="role" required onchange="toggleCompanySelect()" class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                    <option value="karyawan" {{ old('role') === 'karyawan' ? 'selected' : '' }}>Karyawan (Peserta)</option>
                    <option value="tutor" {{ old('role') === 'tutor' ? 'selected' : '' }}>Tutor (Pengajar)</option>
                </select>
                @error('role')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="company_id" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">
                    Perusahaan <span class="text-rose-500">*</span>
                </label>
                
                <!-- Client Companies Select (for Karyawan) -->
                <div id="client_companies_wrapper">
                    <select name="company_id" id="client_company_id" class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                        <option value="">Pilih Perusahaan Klien...</option>
                        @foreach($clientCompanies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>🏢 {{ $company->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[11px] font-semibold text-slate-400">Karyawan wajib diasosiasikan dengan perusahaan klien.</p>
                </div>

                <!-- Startup Entities Select (for Tutor) -->
                <div id="startup_companies_wrapper" class="hidden">
                    <select name="startup_company_id" id="startup_company_id" class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                        @foreach($startupCompanies as $startup)
                            <option value="{{ $startup->id }}" {{ old('company_id') == $startup->id ? 'selected' : '' }}>🚀 {{ $startup->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[11px] font-semibold text-slate-400">Tutor terafiliasi dengan entitas platform startup.</p>
                </div>

                @error('company_id')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Avatar Upload -->
        <div>
            <label for="avatar" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-2">Foto Profil (Opsional)</label>
            <input type="file" name="avatar" id="avatar" accept="image/*"
                class="text-xs font-bold text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-extrabold file:bg-[#534ebb]/10 file:text-[#534ebb] hover:file:bg-[#534ebb]/20 cursor-pointer">
            @error('avatar')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('companies.employees') }}" class="px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-xs font-extrabold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-xs font-extrabold text-white shadow-sm transition">
                Simpan User ➔
            </button>
        </div>
    </form>
</div>

<script>
    function toggleCompanySelect() {
        const role = document.getElementById('role').value;
        const clientWrapper = document.getElementById('client_companies_wrapper');
        const startupWrapper = document.getElementById('startup_companies_wrapper');
        const clientSelect = document.getElementById('client_company_id');
        const startupSelect = document.getElementById('startup_company_id');

        if (role === 'tutor') {
            clientWrapper.classList.add('hidden');
            startupWrapper.classList.remove('hidden');
            clientSelect.removeAttribute('name');
            startupSelect.setAttribute('name', 'company_id');
        } else {
            clientWrapper.classList.remove('hidden');
            startupWrapper.classList.add('hidden');
            startupSelect.removeAttribute('name');
            clientSelect.setAttribute('name', 'company_id');
        }
    }
    document.addEventListener('DOMContentLoaded', toggleCompanySelect);
</script>
@endsection
