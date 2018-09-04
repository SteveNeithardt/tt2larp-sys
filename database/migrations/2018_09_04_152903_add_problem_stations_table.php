<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProblemStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('problem_stations', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('problem_id')->nullable();
			$table->unsignedInteger('step_id')->nullable();
			$table->timestamps();

			$table->foreign('problem_id')->references('id')->on('problems');
			$table->foreign('step_id')->references('id')->on('steps');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('problem_stations');
    }
}
