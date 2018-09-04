<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryStation extends Model
{
	protected $table = 'library_stations';

	/**
	 * Station(s) referencing this Model
	 */
	public function stations()
	{
		return $this->morphMany(Station::class, 'station');
	}
}
