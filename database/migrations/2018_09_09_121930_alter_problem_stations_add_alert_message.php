<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProblemStationsAddAlertMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('problem_stations', function (Blueprint $table) {
			$table->string('alert_message')->after('step_id');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('problem_stations', function (Blueprint $table) {
			$table->dropColumn('alert_message');
		});
    }
}
