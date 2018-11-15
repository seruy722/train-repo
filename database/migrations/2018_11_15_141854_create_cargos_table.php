<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->float('sum', 8, 2)->default(0);
            $table->string('client');
            $table->integer('place')->default(0)->unsigned();
            $table->float('kg', 8, 2)->default(0)->unsigned();
            $table->string('fax')->nullable();
            $table->string('notation')->nullable();
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
        Schema::dropIfExists('cargos');
    }
}
