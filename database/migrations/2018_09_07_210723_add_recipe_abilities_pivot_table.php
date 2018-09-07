<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecipeAbilitiesPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('recipe_abilities', function (Blueprint $table) {
			$table->unsignedInteger('recipe_id');
			$table->unsignedInteger('ability_id')->index();
			$table->primary([ 'recipe_id', 'ability_id' ]);

			$table->foreign('recipe_id')->references('id')->on('recipes');
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
		Schema::dropIfExists('recipes');
    }
}
