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
        $this->call(RoleSeeder::class);

        $lecturer = User::factory()->create([
            'name' => 'Lecturer User',
            'email' => 'lecturer@example.com',
            'password' => bcrypt('password'),
        ]);
        $lecturer->assignRole('lecturer');

        $student = User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => bcrypt('password'),
        ]);
        $student->assignRole('student');
    }
}
