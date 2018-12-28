<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Users seeds
     *
     * @return void
     */

    public function run()
    {
        DB::statement("SET foreign_key_checks=0");
        DB::table('users')->truncate();
        DB::statement("SET foreign_key_checks=1");

        $faker = Faker\Factory::create();
        for ($i = 0; $i < 1000; $i++) {
            $arrRole = ['admin', 'user', 'blackList', 'moder'];
            DB::table('users')->insert([
                'name' => $faker->firstNameFemale.$i,
                'email' => $faker->email.$i,
                'password' => bcrypt('secret'),
                'role' => $faker->randomElement($arrRole),
                'code' => $faker->numberBetween($min = 1, $max = 2000),
            ]);
        }

    }
}
