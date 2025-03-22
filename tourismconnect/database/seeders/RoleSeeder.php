<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Créer un administrateur
        User::create([
            'name' => 'Admin',
            'email' => 'admin@tourismconnect.com',
            'password' => Hash::make('password'),
            'type' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Créer un prestataire de test
        User::create([
            'name' => 'Prestataire Test',
            'email' => 'prestataire@tourismconnect.com',
            'password' => Hash::make('password'),
            'type' => 'prestataire',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Créer un touriste de test
        User::create([
            'name' => 'Touriste Test',
            'email' => 'touriste@tourismconnect.com',
            'password' => Hash::make('password'),
            'type' => 'touriste',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}