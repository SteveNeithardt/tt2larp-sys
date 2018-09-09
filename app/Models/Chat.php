<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
	protected $table = 'chats';

	/**
	 * Get all Messages in Chat
	 */
	public function messages()
	{
		return $this->hasMany(Message::class, 'chat_id', 'id');
	}
}
