<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->increments('id');
            $table->float('for_kg', 6, 2)->unsigned()->default(0);
            $table->float('for_place', 6, 2)->unsigned()->default(0);
            $table->float('for_kg_brand', 4, 2)->unsigned()->default(0);
            $table->float('for_place_brand', 4, 2)->unsigned()->default(0);
            $table->float('for_commission', 3, 2)->unsigned()->default(0);
            $table->integer('client_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices');
    }
}
