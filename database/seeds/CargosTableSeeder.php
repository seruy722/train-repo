<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;

class CargosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks=0");
        DB::table('cargos')->truncate();
        DB::statement("SET foreign_key_checks=1");

        $faker = Faker\Factory::create();
        $clients =  User::all()->pluck('name')->toArray();
        for ($i = 0; $i < 5000; $i++) {
            $arrRole = ['ДОЛГ', 'ОПЛАТА'];
            $type = $faker->randomElement($arrRole);
            $summa = $faker->numberBetween($min = 1, $max = 50000);

            if($type === 'ДОЛГ'){
                $summa *= -1;
            }

            DB::table('cargos')->insert([
                'created_at' => $faker->dateTime($max = 'now', $timezone = null),
                'type' => $type,
                'sum' => $summa,
                'sale' => $type === 'ДОЛГ' ? 0 : $faker->numberBetween($min = 1, $max = 1000),
                'client' => $faker->randomElement($clients),
                'place' => $faker->numberBetween($min = 1, $max = 100),
                'kg' => $faker->numberBetween($min = 1, $max = 1000),
                'fax' => $faker->city,
                'notation' => $faker->sentence($nbWords = 3, $variableNbWords = true),
            ]);
        }
    }
}
