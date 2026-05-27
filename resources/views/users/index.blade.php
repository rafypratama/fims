<x-app-layout>
    <div class="space-y-6">
        <!-- Top header action bar -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Manajemen Pengguna</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola data karyawan, hak akses role admin & karyawan, serta kredensial akun.</p>
            </div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl text-sm hover:bg-indigo-700 transition-colors shadow-sm self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Pengguna
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2 shadow-sm animate-fade-in">
                <span>✅</span>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm flex items-center gap-2 shadow-sm animate-fade-in">
                <span>❌</span>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Users Table Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-4">Nama Lengkap</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4 text-center">Role Akses</th>
                            <th class="py-3 px-4">Terdaftar Pada</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @forelse($users as $usr)
                            <tr class="hover:bg-slate-50/40 transition-colors">
                                <!-- Name & Avatar -->
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $initials = '';
                                            foreach (explode(' ', $usr->name) as $w) {
                                                $initials .= strtoupper(substr($w, 0, 1));
                                            }
                                            $initials = substr($initials, 0, 2) ?: 'U';

                                            $colors = ['bg-indigo-500', 'bg-violet-500', 'bg-sky-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500'];
                                            $colIdx = abs(crc32($usr->name)) % count($colors);
                                            $bgClass = $colors[$colIdx];
                                        @endphp
                                        <div class="w-9 h-9 rounded-full {{ $bgClass }} text-white font-bold flex items-center justify-center text-xs shadow-sm">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-slate-800 block">{{ $usr->name }}</span>
                                            @if($usr->id === auth()->id())
                                                <span class="inline-flex items-center px-1.5 py-0.2 bg-indigo-50 text-indigo-700 text-[9px] font-bold rounded">Sesi Aktif</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="py-4 px-4 text-slate-600 font-medium">
                                    {{ $usr->email }}
                                </td>

                                <!-- Role -->
                                <td class="py-4 px-4 text-center">
                                    @if($usr->isAdmin())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gradient-to-r from-indigo-500 to-violet-500 text-white shadow-sm shadow-indigo-100">
                                            👑 Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            👤 Karyawan
                                        </span>
                                    @endif
                                </td>

                                <!-- Created At -->
                                <td class="py-4 px-4 text-slate-400 text-xs">
                                    {{ $usr->created_at ? $usr->created_at->format('d M Y, H:i') : '-' }}
                                    <div class="text-[10px] text-slate-350 mt-0.5">{{ $usr->created_at ? $usr->created_at->diffForHumans() : '' }}</div>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2.5">
                                        <a href="{{ route('users.edit', $usr->id) }}" class="text-xs font-semibold text-indigo-650 hover:text-indigo-800 transition-colors bg-indigo-50 hover:bg-indigo-100/80 px-3 py-1.5 rounded-lg">
                                            Edit
                                        </a>
                                        @if($usr->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $usr->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $usr->name }}? Semua aktivitas akan tetap tercatat di sistem.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-rose-650 hover:text-rose-800 transition-colors bg-rose-50 hover:bg-rose-100/80 px-3 py-1.5 rounded-lg">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs font-semibold text-slate-300 bg-slate-50 px-3 py-1.5 rounded-lg cursor-not-allowed" title="Tidak dapat menghapus diri sendiri">
                                                Hapus
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-450 italic">Tidak ada pengguna lain terdaftar di database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
