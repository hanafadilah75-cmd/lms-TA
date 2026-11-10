@extends('layouts.app')

@section('page_title', 'Ubah User')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Ubah Data Pengguna</h3>
        <p class="text-xs font-semibold text-slate-400 mt-0.5">Perbarui profil {{ $employee->name }}.</p>
    </div>

    <form action="{{ route('companies.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Name Input -->
        <div>
            <label for="name" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
            <input type="text" name="name" id="name" required value="{{ old('name', $employee->name) }}"
                class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
            @error('name')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email & Password -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label for="email" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Alamat Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" id="email" required value="{{ old('email', $employee->email) }}"
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                @error('email')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">Kata Sandi</label>
                <p class="text-[10px] font-bold text-slate-400 mb-1">Kosongkan jika tidak diubah</p>
                <input type="password" name="password" id="password" placeholder="Minimal 8 karakter"
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
                    <option value="karyawan" {{ old('role', $employee->role) === 'karyawan' ? 'selected' : '' }}>Karyawan (Peserta)</option>
                    <option value="tutor" {{ old('role', $employee->role) === 'tutor' ? 'selected' : '' }}>Tutor (Pengajar)</option>
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
                            <option value="{{ $company->id }}" {{ old('company_id', $employee->company_id) == $company->id ? 'selected' : '' }}>🏢 {{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Startup Entities Select (for Tutor) -->
                <div id="startup_companies_wrapper" class="hidden">
                    <select name="startup_company_id" id="startup_company_id" class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                        @foreach($startupCompanies as $startup)
                            <option value="{{ $startup->id }}" {{ old('company_id', $employee->company_id) == $startup->id ? 'selected' : '' }}>🚀 {{ $startup->name }}</option>
                        @endforeach
                    </select>
                </div>

                @error('company_id')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Avatar Upload -->
        <div>
            <label for="avatar" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-2">Foto Profil (Opsional)</label>
            <div class="flex items-center gap-4 mb-2">
                @if($employee->avatar)
                    <img class="h-14 w-14 rounded-2xl object-cover border border-slate-100 shadow-sm" src="{{ asset('storage/' . $employee->avatar) }}" alt="">
                @endif
                <input type="file" name="avatar" id="avatar" accept="image/*"
                    class="text-xs font-bold text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-extrabold file:bg-[#534ebb]/10 file:text-[#534ebb] hover:file:bg-[#534ebb]/20 cursor-pointer">
            </div>
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
                Simpan Perubahan ➔
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
