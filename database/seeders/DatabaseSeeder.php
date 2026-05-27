<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@fims.local'],
            [
                'name' => 'Admin FIMS',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Karyawan
        User::firstOrCreate(
            ['email' => 'karyawan@fims.local'],
            [
                'name' => 'Karyawan FIMS',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
            ]
        );

        // Bank Accounts
        \App\Models\BankAccount::firstOrCreate(
            ['account_number' => '8110987711'],
            [
                'bank_name' => 'BCA',
                'account_holder' => 'Eva Triwulandari',
            ]
        );

        \App\Models\BankAccount::firstOrCreate(
            ['account_number' => '1320012345678'],
            [
                'bank_name' => 'Mandiri',
                'account_holder' => 'Eva Triwulandari',
            ]
        );

        // System Settings
        \App\Models\SystemSetting::firstOrCreate(['key' => 'company_name'], ['value' => 'Faacos Indonesia']);
        \App\Models\SystemSetting::firstOrCreate(['key' => 'company_phone'], ['value' => '0812-3456-7890']);
        \App\Models\SystemSetting::firstOrCreate(['key' => 'company_address'], ['value' => 'Ruko Emerald No. 12, Jakarta, Indonesia']);
        \App\Models\SystemSetting::firstOrCreate(['key' => 'company_email'], ['value' => 'info@faacos.co.id']);
        \App\Models\SystemSetting::firstOrCreate(['key' => 'signature_name'], ['value' => 'Eva Triwulandari']);
        \App\Models\SystemSetting::firstOrCreate(['key' => 'signature_role'], ['value' => 'Direktur Utama']);
    }
}
