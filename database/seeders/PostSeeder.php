<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Config\DB;
use Config\Hash;
use Config\Log;
use Config\Seeder;

class PostSeeder extends Seeder
{
    public function run()
    {
        // TODO: Add your seed logic here
        $user = DB::table('users')->insert([
            'name'     => 'Test User '.rand(111111,999999),
            'phone'    => '01'.rand(1,9).rand(00000000,99999999),
            'email'    => 'user'.rand(1111,9999).'@gmail.com',
            'password' => Hash::make('1234'),
        ]);

        for ($i=1; $i <= 10; $i++) { 
            Post::create([
                'user_id' => $user->id, 
                'title' => 'Test Post '.$i, 
                'body' => 'Test post body '.$i
            ]);
        }
    }
}
