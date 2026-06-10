<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Looks for the email first. If found, it updates the name/password. If not, it inserts a new row.
        User::updateOrCreate(
            ['email' => 'gaihrenirmal2021@gmail.com'], // The unique column to look up
            [
                'name' => 'Deurali Chemical Pvt. Ltd.',
                'password' => Hash::make('password123'),
                'is_admin' => true, 
            ]
        );
    }
}