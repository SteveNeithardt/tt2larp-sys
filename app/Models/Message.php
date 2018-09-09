<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
	protected $table = 'messages';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var  array
	 */
	protected $fillable = [ 'message', 'user_id' ];

	/**
	 * Chat instance this Message is attached to
	 */
	public function chat()
	{
		return $this->belongsTo(Chat::class, 'chat_id', 'id');
	}

	/**
	 * User that spawned this Message
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	/**
	 * Helper scope for getting $amount recent messages at $offset
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder $query
	 * @param  integer $amount, number of expected results
	 * @param  integer $offset, number of records to skip before getting $amount
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeRecent($query, $amount = 30, $offset = 0)
	{
		return $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->skip($offset)->take($amount);
	}

	/**
	 * Format created_at into just the time
	 */
	public function getTimeAttribute()
	{
		return $this->created_at->format('Hms');
	}
}
