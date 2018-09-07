<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class CraftingStation extends Model
{
	protected $table = 'crafting_stations';

	/**
	 * Station(s) referencing this Model
	 */
	public function stations()
	{
		return $this->morphMany(Station::class, 'station');
	}
}
