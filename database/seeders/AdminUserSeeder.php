<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari role Owner, atau buat jika belum ada
        $role = Role::firstOrCreate(['name' => 'Owner']);

        // Buat atau perbarui user admin
        User::updateOrCreate(
            ['email' => 'emfaska50@gmail.com'],
            [
                'name'              => 'Admin AJM Store',
                'email'             => 'emfaska50@gmail.com',
                'password'          => Hash::make('penakonco'),
                'role_id'           => $role->id,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ User admin berhasil dibuat: emfaska50@gmail.com');
    }
}
