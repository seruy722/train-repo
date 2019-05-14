<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileIdToFaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faxes', function (Blueprint $table) {
            $table->integer('file_id')->unsigned()->after('air_or_car');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faxes', function (Blueprint $table) {
            $table->dropColumn('file_id');
        });
    }
}
