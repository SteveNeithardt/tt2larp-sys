<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

use tt2larp\Traits\HasCodeTrait;

class Recipe extends Model
{
	use HasCodeTrait;

	protected $table = 'recipes';

	/**
	 * relations to always eager load
	 */
	protected $with = [
		'codes',
		'ingredients'
	];

	/**
	 * upon boot, declare what happens on delete
	 */
	protected static function boot()
	{
		parent::boot();

		static::deleting(function ($recipe) {
			$recipe->craft()->dissociate();
			$recipe->codes()->delete();
			foreach ($recipe->ingredients as $ingredient) {
				$ingredient->delete();
			}
		});
	}

	/**
	 * Abilities this Recipe needs
	 */
	public function abilities()
	{
		return $this->belongsToMany(Ability::class, 'recipe_abilities', 'recipe_id', 'ability_id');
	}

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
