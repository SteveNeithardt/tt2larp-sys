<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

use tt2larp\Traits\HasCodeTrait;

class Character extends Model
{
	use HasCodeTrait;

	protected $table = 'characters';

	/**
	 * upon boot, declare what happens on delete
	 */
	protected static function boot()
	{
		parent::boot();

		static::deleting(function ($character) {
			$character->codes()->delete();
			$character->abilities()->detach();
		});
	}

	/**
	 * All abilities a character has
	 */
	public function abilities()
	{
		return $this->belongsToMany(Ability::class, 'character_abilities', 'character_id', 'ability_id')->withPivot('value');
	}
}
