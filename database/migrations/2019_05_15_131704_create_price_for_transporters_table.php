<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceForTransportersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_for_transporters', function (Blueprint $table) {
            $table->increments('id');
            $table->float('for_kg', 8, 2)->default(0)->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('transporter_id')->unsigned();
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
        Schema::dropIfExists('price_for_transporters');
    }
}
