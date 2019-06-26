<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DestroyFieldNameOfThingsAndForPlaceFromFaxesMoreInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faxes_more_info', function (Blueprint $table) {
            $table->dropColumn('name_of_things', 'for_place');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faxes_more_info', function (Blueprint $table) {
            //
        });
    }
}
