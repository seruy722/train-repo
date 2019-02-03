<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faxes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fax_name');
            $table->string('upload_date');
            $table->string('uploaded_to_table_cargos_date');
            $table->boolean('uploaded_to_table_cargos')->default(false);
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
        Schema::dropIfExists('faxes');
    }
}
