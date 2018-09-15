<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPartsAbilitiesPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('parts_abilities', function (Blueprint $table) {
			$table->unsignedInteger('part_id')->index();
			$table->unsignedInteger('ability_id')->index();
			$table->integer('value');

			$table->foreign('part_id')->references('id')->on('parts');
			$table->foreign('ability_id')->references('id')->on('abilities');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('parts_abilities');
    }
}
