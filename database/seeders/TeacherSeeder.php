<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'ចាន់ សុភា',
            'email'    => 'teacher@university.edu',
            'password' => Hash::make('teacher1234'),
            'role'     => 'teacher',
        ]);
    }
}
