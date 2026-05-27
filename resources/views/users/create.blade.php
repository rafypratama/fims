<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-slate-500">
                ← Kembali
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Tambah Pengguna Baru</h1>
                <p class="text-sm text-slate-500 mt-1">Daftarkan akun karyawan baru dan tentukan hak akses peran mereka.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm space-y-1">
                <span class="font-bold block">Silakan perbaiki kesalahan berikut:</span>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Form -->
        <form action="{{ route('users.store') }}" method="POST" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            @csrf

            <!-- Nama Lengkap -->
            <div class="space-y-1.5">
                <label for="name" class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Contoh: budi@fims.local" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
            </div>

            <!-- Role / Hak Akses -->
            <div class="space-y-1.5">
                <label for="role" class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Hak Akses Role</label>
                <select name="role" id="role" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="karyawan" {{ old('role') === 'karyawan' ? 'selected' : '' }}>Karyawan (Hanya membuat Surat Jalan & Invoice)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Akses penuh ke seluruh sistem & Pengaturan)</option>
                </select>
            </div>

            <!-- Sandi & Konfirmasi Sandi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Kata Sandi</label>
                    <input type="password" name="password" id="password" required placeholder="Minimal 8 karakter" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                </div>
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ketik ulang kata sandi" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                </div>
            </div>

            <!-- Actions Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <a href="{{ route('users.index') }}" class="px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-700 font-semibold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm transition-colors shadow-sm">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
