<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@thesis.pl',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Supervisors
        User::create([
            'name' => 'Dr Jan Kowalski',
            'email' => 'kowalski@thesis.pl',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
        ]);

        User::create([
            'name' => 'Prof. Anna Nowak',
            'email' => 'nowak@thesis.pl',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
        ]);

        User::create([
            'name' => 'Dr Piotr Wiśniewski',
            'email' => 'wisniewski@thesis.pl',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
        ]);

        // Students
        User::create([
            'name' => 'Michał Kowalczyk',
            'email' => 'student1@thesis.pl',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Karolina Lewandowska',
            'email' => 'student2@thesis.pl',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Tomasz Zieliński',
            'email' => 'student3@thesis.pl',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Agnieszka Szymańska',
            'email' => 'student4@thesis.pl',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Jakub Dąbrowski',
            'email' => 'student5@thesis.pl',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $this->command->info('✅ Test users created:');
        $this->command->info('   Admin: admin@thesis.pl / password');
        $this->command->info('   Supervisors: kowalski@thesis.pl, nowak@thesis.pl, wisniewski@thesis.pl / password');
        $this->command->info('   Students: student1-5@thesis.pl / password');
    }
}
