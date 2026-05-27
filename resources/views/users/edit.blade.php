<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-slate-500">
                ← Kembali
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Ubah Data Pengguna</h1>
                <p class="text-sm text-slate-500 mt-1">Perbarui profil karyawan, hak akses role, atau atur ulang kata sandi.</p>
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
        <form action="{{ route('users.update', $user->id) }}" method="POST" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            @csrf
            @method('PUT')

            <!-- Nama Lengkap -->
            <div class="space-y-1.5">
                <label for="name" class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required placeholder="Contoh: Budi Santoso" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required placeholder="Contoh: budi@fims.local" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
            </div>

            <!-- Role / Hak Akses -->
            <div class="space-y-1.5">
                <label for="role" class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Hak Akses Role</label>
                <select name="role" id="role" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                    <option value="karyawan" {{ old('role', $user->role) === 'karyawan' ? 'selected' : '' }}>Karyawan (Hanya membuat Surat Jalan & Invoice)</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (Akses penuh ke seluruh sistem & Pengaturan)</option>
                </select>
                @if($user->id === auth()->id())
                    <input type="hidden" name="role" value="{{ $user->role }}" />
                    <span class="text-xs text-slate-400 mt-1 block">💡 Anda tidak dapat mengubah peran/role akun Anda sendiri yang sedang aktif.</span>
                @endif
            </div>

            <!-- Reset Sandi Info Alert -->
            <div class="p-3.5 bg-blue-50 border border-blue-150 rounded-lg text-xs text-blue-750 space-y-1">
                <span class="font-semibold block">🔐 Pengaturan Ulang Kata Sandi (Opsional)</span>
                <p>Biarkan kedua kolom di bawah ini kosong jika Anda tidak ingin mengubah kata sandi pengguna saat ini.</p>
            </div>

            <!-- Sandi & Konfirmasi Sandi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Kata Sandi Baru</label>
                    <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                </div>
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ketik ulang kata sandi baru" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                </div>
            </div>

            <!-- Actions Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <a href="{{ route('users.index') }}" class="px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-700 font-semibold text-sm transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
