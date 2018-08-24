<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
	protected $table = 'characters';

	/**
	 * All abilities a character has
	 */
	public function abilities()
	{
		$this->belongsToMany(Ability::class, 'character_abilities', 'character_id', 'ability_id');
	}
}
