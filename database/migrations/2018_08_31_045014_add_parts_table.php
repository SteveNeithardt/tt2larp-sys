<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('parts', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('article_id');
			$table->string('name');
			$table->text('description');
			$table->unsignedInteger('ability_id')->nullable();
			$table->integer('min_value')->nullable();
			$table->timestamps();

			$table->foreign('article_id')->references('id')->on('articles');
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
		Schema::dropIfExists('parts');
    }
}
