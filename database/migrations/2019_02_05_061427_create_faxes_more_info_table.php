<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaxesMoreInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faxes_more_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->nullable();
            $table->integer('client_id')->unsigned();
            $table->integer('fax_id')->unsigned();
            $table->integer('place')->default(0)->unsigned();
            $table->float('kg', 8, 2)->default(0)->unsigned();
            $table->string('shop')->nullable();
            $table->jsonb('name_of_things')->nullable();
            $table->boolean('brand')->default(false);
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
        Schema::dropIfExists('faxes_more_info');
    }
}
