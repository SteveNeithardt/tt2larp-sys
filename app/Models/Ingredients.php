<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

use tt2larp\Traits\HasCodeTrait;

class Ingredient extends Model
{
	use HasCodeTrait;

	protected $table = 'ingredients';

	/**
	 * relations to always eager load
	 */
	protected $with = [
		'codes',
	];

	/**
	 * upon boot, declare what happens on delete
	 */
	protected static function boot()
	{
		parent::boot();

		static::deleting(function ($ingredient) {
			$ingredient->codes()->delete();
			$ingredient->recipes()->detach();
		});
	}

	/**
	 * Recipes that use this Ingredient
	 */
	public function recipes()
	{
		return $this->belongsToMany(Ingredient::class, 'recipe_ingredients', 'ingredient_id', 'recipe_id');
	}

	/**
	 * Recipe that creates this Ingredient
	 */
	public function recipe()
	{
		return $this->hasOne(Recipe::class, 'craft_id', 'id');
	}
}
