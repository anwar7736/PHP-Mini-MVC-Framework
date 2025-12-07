<?php

namespace Database\Seeders;
use Config\Seeder;
use App\Models\Model;
use App\Models\Student;

class UsersSeeder extends Seeder
{
    public function run()
    {
        Student::create([
            'name' => 'Test Student_'.rand()
        ]);
        // TODO: Add your seed logic here
    }
}
