<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterArticlesAddLibraryStation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('articles', function (Blueprint $table) {
			$table->unsignedInteger('library_station_id')->after('id')->index()->nullable();

			$table->foreign('library_station_id')->references('id')->on('library_stations');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('articles', function (Blueprint $table) {
			$table->dropColumn('library_station_id');
		});
    }
}
