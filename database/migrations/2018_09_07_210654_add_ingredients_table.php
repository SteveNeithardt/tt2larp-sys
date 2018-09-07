<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIngredientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('ingredients', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->unsignedInteger('craft_id')->index()->nullable();
			$table->timestamps();

			$table->foreign('craft_id')->references('id')->on('recipes');
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
