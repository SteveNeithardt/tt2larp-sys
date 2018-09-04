<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
	/**
	 * No timestamps on this instance
	 */
	public $timestamps = false;

	/**
	 * Get all of the owning station models
	 */
	public function station()
	{
		return $this->morphTo();
	}
}
