<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldForKgAndForPlace extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faxes_more_info', function (Blueprint $table) {
            $table->integer('for_place')->default(0)->unsigned()->after('notation');
            $table->float('for_kg', 8, 2)->default(0)->unsigned()->after('notation');
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
            $table->dropColumn('for_place', 'for_kg');
        });
    }
}
