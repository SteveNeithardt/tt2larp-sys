<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStepNextStepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('step_next_steps', function (Blueprint $table) {
			$table->dropColumn('type');
			$table->dropForeign(['ability_id']);
			$table->dropColumn('ability_id');
			$table->dropColumn('min_value');
			$table->dropColumn('code');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		// nope, will refactor this anyway
    }
}
