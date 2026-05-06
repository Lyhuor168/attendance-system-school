<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name'     => 'លី មករា',
            'email'    => 'student@university.edu',
            'password' => Hash::make('student1234'),
            'role'     => 'student',
        ]);

        Student::create([
            'user_id'      => $user->id,
            'student_code' => 'STU001',
            'class_id'     => null,
        ]);
    }
}