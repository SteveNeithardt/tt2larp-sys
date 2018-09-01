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
			$table->unsignedInteger('character_id');
			$table->unsignedInteger('ability_id');
			$table->integer('value');

			$table->primary(['character_id', 'ability_id']);
			$table->foreign('character_id')->references('id')->on('characters');
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
		Schema::dropIfExists('character_abilities');
    }
}
