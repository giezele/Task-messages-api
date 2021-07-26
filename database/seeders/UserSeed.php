<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        // $user = User::create([
        DB::table('users')->insert([
            'name' => 'admin',
           'email' => 'admin@admin.com',
           'password' => 'admin123',
           'is_admin' => true,
        ]);

        // generate a few more users 
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => Hash::make($faker->password),
                
            ]);
        }
        // $user->attach('task');

    }
}
