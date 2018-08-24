<?php

namespace tt2larp;

use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
	protected $table = 'abilities';

	/**
	 * All characters that have this ability
	 */
	public function characters()
	{
		$this->belongsToMany(Character::class, 'character_abilities', 'ability_id', 'character_id');
	}
}
