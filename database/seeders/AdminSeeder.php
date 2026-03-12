<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Kunci pencarian
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin123'),
                'usertype' => 'admin', 
                'status' => true,
                'email_verified_at' => now(), 
            ]
        );
    }
}
