<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

use tt2larp\Traits\HasCodeTrait;

class Recipe extends Model
{
	use HasCodeTrait;

	protected $table = 'recipes';

	/**
	 * Ingredients this Recipe uses
	 */
	public function ingredients()
	{
		return $this->belongsToMany(Ingredient::class, 'recipe_ingredients', 'recipe_id', 'ingredient_id');
	}

	/**
	 * Ingredient created by this Recipe
	 */
	public function craft()
	{
		return $this->belongsTo(Ingredient::class, 'craft_id', 'id');
	}
}
