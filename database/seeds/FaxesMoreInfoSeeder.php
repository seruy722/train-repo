<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;

class FaxesMoreInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks=0");
        DB::table('faxes_more_info')->truncate();
        DB::statement("SET foreign_key_checks=1");

        $faker = Faker\Factory::create();
        $clients = User::all()->toArray();
        for ($i = 0; $i < 15000; $i++) {
            $brand = [true, false];
            $faxID = $faker->numberBetween($min = 1, $max = 50000);
            $randNumber = rand(1, 1000);
            $randClient = $clients[$randNumber];
            $arr = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);

            DB::table('faxes_more_info')->insert([
                'code' => $faker->unixTime($max = 'now'),
                'client_id' => $randClient['id'],
                'fax_id' => $faxID,
                'place' => $faker->numberBetween($min = 1, $max = 100),
                'kg' => $faker->numberBetween($min = 1, $max = 1000),
                'brand' => $faker->randomElement($brand),
                'name_of_things' => json_encode($arr),
                'shop' => $faker->randomElement($brand),
                'notation' => $faker->sentence($nbWords = 3, $variableNbWords = true),
            ]);
        }
    }
}
