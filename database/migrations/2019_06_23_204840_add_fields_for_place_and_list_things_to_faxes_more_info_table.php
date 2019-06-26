<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsForPlaceAndListThingsToFaxesMoreInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faxes_more_info', function (Blueprint $table) {
            $table->float('for_place', 8, 2)->default(0)->unsigned()->after('for_kg');
            $table->text('list_things')->nullable()->after('shop');
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
            $table->dropColumn('for_place', 'list_things');
        });
    }
}
