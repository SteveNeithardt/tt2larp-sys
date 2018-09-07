<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

use tt2larp\Traits\HasCodeTrait;

class Ingredient extends Model
{
	use HasCodeTrait;

	protected $table = 'ingredient';

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
