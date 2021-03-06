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
        $clients = User::all()->toArray();
        for ($i = 0; $i < 5000; $i++) {
            $arrRole = ['ДОЛГ', 'ОПЛАТА'];
            $type = $faker->randomElement($arrRole);
            $summa = $faker->numberBetween($min = 1, $max = 50000);
            $randNumber = rand(1, 1000);
            $randClient = $clients[$randNumber];

            if($type === 'ДОЛГ'){
                $summa *= -1;
            }

            DB::table('debts')->insert([
                'created_at' => $faker->dateTime($max = 'now', $timezone = null),
                'type' => $type,
                'sum' => $summa,
                'client_id' => $randClient['id'],
                'notation' => $faker->sentence($nbWords = 3, $variableNbWords = true),
                'commission' => $faker->numberBetween($min = 1, $max = 1000),
            ]);
        }
    }
}
