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
        $clients = User::all()->toArray();
        for ($i = 0; $i < 5000; $i++) {
            $arrRole = ['ДОЛГ', 'ОПЛАТА'];
            $type = $faker->randomElement($arrRole);
            $sum = $faker->numberBetween($min = 1, $max = 50000);
            $randNumber = rand(1, 1000);
            $randClient = $clients[$randNumber];
            if ($type === 'ДОЛГ') {
                $sum *= -1;
            }

            DB::table('cargos')->insert([
                'created_at' => $faker->dateTime($max = 'now', $timezone = null),
                'type' => $type,
                'sum' => $sum,
                'sale' => $type === 'ДОЛГ' ? 0 : $faker->numberBetween($min = 1, $max = 1000),
                'client' => $randClient['name'],
                'client_id' => $randClient['id'],
                'place' => $faker->numberBetween($min = 1, $max = 100),
                'kg' => $faker->numberBetween($min = 1, $max = 1000),
                'fax' => $faker->city,
                'notation' => $faker->sentence($nbWords = 3, $variableNbWords = true),
            ]);
        }
    }
}
