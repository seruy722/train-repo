<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UsersTableSeeder::class);
         $this->call(CargosTableSeeder::class);
         $this->call(DebtsTableSeeder::class);
         $this->call(CargosTableSeeder::class);
         $this->call(PricesTableSeeder::class);
         $this->call(FaxesTableSeeder::class);
         $this->call(FaxesMoreInfoSeeder::class);
    }
}
