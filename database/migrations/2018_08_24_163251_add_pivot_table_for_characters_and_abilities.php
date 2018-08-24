<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPivotTableForCharactersAndAbilities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('character_abilities', function (Blueprint $table) {
			$table->unsignedInteger('ability_id');
			$table->unsignedInteger('character_id');
			$table->integer('value');

			$table->foreign('ability_id')->references('id')->on('abilities');
			$table->foreign('character_id')->references('id')->on('characters');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropTable('character_abilities');
    }
}
