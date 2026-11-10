@extends('layouts.app')

@section('page_title', 'Pengaturan Profil')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Profil Anda</h3>
        <p class="text-xs font-semibold text-slate-400 mt-0.5">Perbarui informasi data diri dan kata sandi akun Anda.</p>
    </div>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Name Input -->
        <div>
            <label for="name" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
            <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}"
                class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
            @error('name')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Input -->
        <div>
            <label for="email" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1.5">Alamat Email <span class="text-rose-500">*</span></label>
            <input type="email" name="email" id="email" required value="{{ old('email', $user->email) }}"
                class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
            @error('email')
                <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Inputs (Optional) -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 pt-4 border-t border-slate-100">
            <div>
                <label for="password" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">Kata Sandi Baru</label>
                <p class="text-[10px] font-bold text-slate-400 mb-1.5">Kosongkan jika tidak ingin mengubah</p>
                <input type="password" name="password" id="password" placeholder="Minimal 8 karakter"
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
                @error('password')
                    <p class="mt-1 text-xs font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">Konfirmasi Kata Sandi</label>
                <p class="text-[10px] font-bold text-slate-400 mb-1.5">Ulangi kata sandi baru</p>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi kata sandi"
                    class="w-full border border-slate-200 rounded-2xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-[#534ebb] focus:ring-2 focus:ring-[#534ebb]/20 transition">
            </div>
        </div>

        <!-- Avatar Upload -->
        <div class="pt-4 border-t border-slate-100">
            <label class="block text-[11px] font-extrabold uppercase text-slate-500 mb-3">Foto Profil / Avatar</label>
            <div class="flex items-center gap-5">
                @if($user->avatar)
                    <img class="h-16 w-16 rounded-2xl object-cover border border-slate-100 shadow-sm" src="{{ asset('storage/' . $user->avatar) }}" alt="">
                @else
                    <div class="h-16 w-16 rounded-2xl bg-[#534ebb]/10 text-[#534ebb] flex items-center justify-center font-extrabold text-2xl uppercase">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                @endif
                <input type="file" name="avatar" id="avatar" accept="image/*"
                    class="text-xs font-bold text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-extrabold file:bg-[#534ebb]/10 file:text-[#534ebb] hover:file:bg-[#534ebb]/20 cursor-pointer">
            </div>
            @error('avatar')
                <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit" class="px-7 py-3 rounded-2xl bg-[#534ebb] hover:bg-[#433ea3] text-white text-xs font-extrabold shadow-sm transition">
                Simpan Perubahan Profil ➔
            </button>
        </div>
    </form>
</div>
@endsection
