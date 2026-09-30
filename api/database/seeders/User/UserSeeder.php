<?php

namespace Database\Seeders\User;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder {
    public function run(): void {
        User::create([
            'name' => 'MotoFlow Admin',
            'email' => 'admin@motoflow.com',
            'password' => 'Moto4521*',
        ]);
    }
}
