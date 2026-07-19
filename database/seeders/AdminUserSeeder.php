<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // रोल पहिले नै बनेको सुनिश्चित गर्न RoleSeeder लाई यहाँ पनि कल गर्न सकिन्छ
        $this->call([RoleSeeder::class]);

        $user = User::updateOrCreate(
            ['email' => 'gaihrenirmal2021@gmail.com'],
            [
                'name' => 'Deurali Chemical Pvt. Ltd.',
                'password' => Hash::make('password123'),
                'is_admin' => true, 
                'role' => 'admin',
            ]
        );

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('admin');
        }
    }
}