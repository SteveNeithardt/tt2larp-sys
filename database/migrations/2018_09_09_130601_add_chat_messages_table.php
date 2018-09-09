<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('messages', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('chat_id')->index();
			$table->unsignedInteger('user_id')->index()->nullable();
			$table->text('message');
			$table->timestamps();

			$table->foreign('chat_id')->references('id')->on('chats');
			$table->foreign('user_id')->references('id')->on('users');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('messages');
    }
}
