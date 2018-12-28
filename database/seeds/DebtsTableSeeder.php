<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;

class DebtsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks=0");
        DB::table('debts')->truncate();
        DB::statement("SET foreign_key_checks=1");

        $faker = Faker\Factory::create();
        $clients = User::all()->pluck('name')->toArray();
        for ($i = 0; $i < 5000; $i++) {
            $arrRole = ['ДОЛГ', 'ОПЛАТА'];
            $type = $faker->randomElement($arrRole);
            DB::table('debts')->insert([
                'created_at' => $faker->dateTime($max = 'now', $timezone = null),
                'type' => $type,
                'sum' => $faker->numberBetween($min = 1, $max = 50000),
                'client' => $faker->randomElement($clients),
                'notation' => $faker->sentence($nbWords = 3, $variableNbWords = true),
                'commission' => $faker->numberBetween($min = 1, $max = 1000),
            ]);
        }
    }
}
