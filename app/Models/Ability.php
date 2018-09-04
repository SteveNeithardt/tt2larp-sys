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

	/**
	 * Return a list of valid abilities from the $second argument.
	 *
	 * @param  $first array of Ability with pivot
	 * @param  $second array of Ability with pivot
	 * @return array of abilities
	 */
	static public function CompareAllInFirst($first, $second)
	{
		$valid = [];
		foreach ($first as $target) {
			foreach ($second as $candidate) {
				if ($target->id === $candidate->id) {
					if ($target->pivot->value <= $candidate->pivot->value) {
						$valid = $candidate;
					}
				}
			}
		}
		return $valid;
	}
}
