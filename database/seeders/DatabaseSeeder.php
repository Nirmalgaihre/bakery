<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // १. रोलहरू सिर्जना गर्ने
        $this->call([
            RoleSeeder::class,
        ]);

        // २. युजर छ भने अपडेट गर्ने, छैन भने मात्र नयाँ बनाउने (Duplicate Error रोक्न)
        $admin = User::updateOrCreate(
            ['email' => 'gaihrenirmal2021@gmail.com'], // यो ईमेल चेक गर्छ
            [
                'name' => 'Admin',
                'password' => Hash::make('password'), // आवश्यक्ता अनुसार पासवर्ड बदल्नुहोला
                'role' => 'admin', 
                'is_admin' => 1,
                'email_verified_at' => now(),
            ]
        );

        // ३. युजरलाई सेफली रोल असाइन गर्ने
        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('admin');
        }
    }
}