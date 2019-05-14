<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;

class PricesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks=0");
        DB::table('prices')->truncate();
        DB::statement("SET foreign_key_checks=1");

        $faker = Faker\Factory::create();
        for ($i = 0; $i < 2000; $i++) {

            DB::table('prices')->insert([
                'client_id' => $i+1,
                'for_kg' => $faker->randomFloat($nbMaxDecimals = 1, $min = 1.5, $max = 8.5),
                'for_place' => $faker->randomFloat($nbMaxDecimals = 1, $min = 2.5, $max = 5),
                'for_kg_brand' => $faker->randomFloat($nbMaxDecimals = 1, $min = 1.5, $max = 8.5),
                'for_place_brand' => $faker->randomFloat($nbMaxDecimals = 1, $min = 2.5, $max = 5),
                'for_commission' => $faker->randomFloat($nbMaxDecimals = 1, $min = 0, $max = 2),
            ]);
        }
    }
}
