<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'company_name' => SystemSetting::getByKey('company_name', 'Faacos Indonesia'),
            'company_phone' => SystemSetting::getByKey('company_phone', '0812-3456-7890'),
            'company_address' => SystemSetting::getByKey('company_address', 'Ruko Emerald No. 12, Jakarta, Indonesia'),
            'company_email' => SystemSetting::getByKey('company_email', 'info@faacos.co.id'),
            'signature_name' => SystemSetting::getByKey('signature_name', 'Eva Triwulandari'),
            'signature_role' => SystemSetting::getByKey('signature_role', 'Direktur Utama'),
        ];

        $bankAccounts = BankAccount::all();

        return view('settings.index', compact('settings', 'bankAccounts'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_phone' => 'required|string|max:255',
            'company_address' => 'required|string',
            'company_email' => 'required|email|max:255',
            'signature_name' => 'required|string|max:255',
            'signature_role' => 'required|string|max:255',
            // Optional new bank account
            'new_bank_name' => 'nullable|string|max:50',
            'new_account_number' => 'nullable|string|max:50',
            'new_account_holder' => 'nullable|string|max:100',
        ]);

        foreach (['company_name', 'company_phone', 'company_address', 'company_email', 'signature_name', 'signature_role'] as $key) {
            SystemSetting::setByKey($key, $validated[$key]);
        }

        if ($request->filled('new_bank_name') && $request->filled('new_account_number') && $request->filled('new_account_holder')) {
            BankAccount::create([
                'bank_name' => $validated['new_bank_name'],
                'account_number' => $validated['new_account_number'],
                'account_holder' => $validated['new_account_holder'],
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan sistem berhasil disimpan.');
    }

    public function deleteBank(BankAccount $bank)
    {
        $bank->delete();
        return back()->with('success', 'Rekening bank berhasil dihapus.');
    }
}
