<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengaturan Sistem (FIMS)</h1>
            <p class="text-sm text-slate-550 mt-1">Konfigurasi informasi cetak dokumen, rekening bank tujuan transfer, dan tanda tangan digital.</p>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Company Info & Signatures Form -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
                <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Profil Perusahaan & Cetakan</h2>
                <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
                    @csrf

                    <!-- Company Name -->
                    <div class="space-y-1">
                        <label for="company_name" class="text-sm font-semibold text-slate-700">Nama Perusahaan <span class="text-rose-500">*</span></label>
                        <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $settings['company_name']) }}" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Phone -->
                        <div class="space-y-1">
                            <label for="company_phone" class="text-sm font-semibold text-slate-700">No. Telepon Perusahaan <span class="text-rose-500">*</span></label>
                            <input type="text" id="company_phone" name="company_phone" value="{{ old('company_phone', $settings['company_phone']) }}" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                        </div>

                        <!-- Email -->
                        <div class="space-y-1">
                            <label for="company_email" class="text-sm font-semibold text-slate-700">Email Resmi <span class="text-rose-500">*</span></label>
                            <input type="email" id="company_email" name="company_email" value="{{ old('company_email', $settings['company_email']) }}" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="space-y-1">
                        <label for="company_address" class="text-sm font-semibold text-slate-700">Alamat Perusahaan <span class="text-rose-500">*</span></label>
                        <textarea id="company_address" name="company_address" rows="3" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors">{{ old('company_address', $settings['company_address']) }}</textarea>
                    </div>

                    <div class="border-t border-slate-100 pt-4 space-y-4">
                        <h3 class="font-bold text-slate-900 text-sm">Penandatangan Dokumen (Tanda Tangan)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Signature Name -->
                            <div class="space-y-1">
                                <label for="signature_name" class="text-sm font-semibold text-slate-700">Nama Penandatangan <span class="text-rose-500">*</span></label>
                                <input type="text" id="signature_name" name="signature_name" value="{{ old('signature_name', $settings['signature_name']) }}" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                            </div>

                            <!-- Signature Role -->
                            <div class="space-y-1">
                                <label for="signature_role" class="text-sm font-semibold text-slate-700">Jabatan / Peran <span class="text-rose-500">*</span></label>
                                <input type="text" id="signature_role" name="signature_role" value="{{ old('signature_role', $settings['signature_role']) }}" required class="w-full py-2 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-colors" />
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4 space-y-4">
                        <h3 class="font-bold text-slate-900 text-sm">Tambah Rekening Bank Baru (Opsional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Bank Name -->
                            <div class="space-y-1">
                                <label for="new_bank_name" class="text-xs font-semibold text-slate-750">Nama Bank</label>
                                <input type="text" id="new_bank_name" name="new_bank_name" placeholder="Contoh: BCA / Mandiri" class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>
                            <!-- Account Number -->
                            <div class="space-y-1">
                                <label for="new_account_number" class="text-xs font-semibold text-slate-750">Nomor Rekening</label>
                                <input type="text" id="new_account_number" name="new_account_number" placeholder="Contoh: 12345678" class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>
                            <!-- Holder Name -->
                            <div class="space-y-1">
                                <label for="new_account_holder" class="text-xs font-semibold text-slate-750">Nama Pemilik</label>
                                <input type="text" id="new_account_holder" name="new_account_holder" placeholder="Nama Penerima" class="w-full py-1.5 px-3 text-sm border border-slate-250 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg text-sm hover:bg-indigo-700 transition-colors shadow-sm">
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right: Bank Accounts List -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4 h-fit">
                <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Daftar Rekening Bank</h2>
                <div class="space-y-2">
                    @forelse($bankAccounts as $bank)
                        <div class="flex items-center justify-between p-3 border border-slate-100 rounded-lg bg-slate-50/50 hover:bg-slate-50 transition-colors text-sm">
                            <div>
                                <span class="font-bold text-slate-900">{{ $bank->bank_name }}</span> - 
                                <span class="text-slate-600 font-mono text-xs">{{ $bank->account_number }}</span>
                                <div class="text-xs text-slate-500 font-medium">a/n {{ $bank->account_holder }}</div>
                            </div>
                            <form action="{{ route('settings.bank.destroy', $bank->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekening bank ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-600 hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-4 italic">Belum ada rekening bank yang dikonfigurasi.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
