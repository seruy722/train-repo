<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryIdToTableFaxesMoreInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faxes_more_info', function (Blueprint $table) {
            $table->integer('category_id')->unsigned()->default(1)->after('name_of_things');
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
            $table->dropColumn('category_id');
        });
    }
}
