<?php

use Illuminate\Database\Seeder;

class FaxesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks=0");
        DB::table('faxes')->truncate();
        DB::statement("SET foreign_key_checks=1");

        $faker = Faker\Factory::create();
        for ($i = 0; $i < 5000; $i++) {
            $arrUploaded = [true, false];

            DB::table('faxes')->insert([
                'created_at' => $faker->dateTime($max = 'now', $timezone = null),
                'fax_name' => $faker->word,
                'date_departure' => $faker->dateTime($max = 'now', $timezone = null),
                'uploaded_to_table_cargos_date' => $faker->dateTime($max = 'now', $timezone = null),
                'uploaded_to_table_cargos' => array_rand($arrUploaded, 1),
            ]);
        }
    }
}
