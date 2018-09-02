<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
	protected $table = 'abilities';

	/**
	 * All characters that have this ability
	 */
	public function characters()
	{
		return $this->belongsToMany(Character::class, 'character_abilities', 'ability_id', 'character_id')->withPivot('value');
	}

	/**
	 * Collapse an array of Characters into their cumulative sum of abilities
	 *
	 * @param  array of Character
	 * @return array of (ability_id => total_value)
	 */
	static public function CollapseCharacters($characters)
	{
		$abilities = [];
		foreach ($characters as $character) {
			foreach ($character->abilities as $ability) {
				if (!isset($abilities[$ability->id])) {
					$abilities[$ability->id] = 0;
				}
				$abilities[$ability->id] += $ability->pivot->value;
			}
		}
		return $abilities;
	}
}
