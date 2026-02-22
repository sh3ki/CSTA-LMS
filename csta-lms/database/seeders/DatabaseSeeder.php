<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Admin Account
        User::updateOrCreate(
            ['id_number' => 'ADMIN-001'],
            [
                'full_name'      => 'System Administrator',
                'contact_number' => null,
                'role'           => 'admin',
                'status'         => true,
                'password'       => \Illuminate\Support\Facades\Hash::make('admin123'),
            ]
        );

        $this->command->info('Admin account created: ADMIN-001 / admin123');
    }
}
