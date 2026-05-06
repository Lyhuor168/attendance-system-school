<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classes;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['name' => 'A1', 'subject' => 'Computer Science', 'teacher' => 'ចាន់ សុភា'],
            ['name' => 'A2', 'subject' => 'Mathematics',      'teacher' => 'លី វិរៈ'],
            ['name' => 'B1', 'subject' => 'English',          'teacher' => 'ហេង នីតា'],
        ];

        foreach ($classes as $class) {
            Classes::create($class);
        }
    }
}
