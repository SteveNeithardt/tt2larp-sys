<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 'code' ];

	/**
	 * No timestamps on this instance
	 */
	public $timestamps = false;

	/**
	 * Primary key is a string
	 */
	public $incrementing = false;

	/**
	 * Primary key is field $code
	 */
	protected $primaryKey = 'code';

	/**
	 * Get all of the owning coded models
	 */
	public function coded()
	{
		return $this->morphTo();
	}
}
