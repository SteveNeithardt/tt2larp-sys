<?php

namespace tt2larp\Http\Controllers;

use Validator;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Ability;
use tt2larp\Models\Ingredient;
use tt2larp\Models\Recipe;

class CraftingController extends Controller
{
	/**
	 * Main entry point for Recipes, everything happens in Vuejs
	 */
	public function portal()
	{
		return view('crafting.portal');
	}

	/**
	 * return all Recipes
	 */
	public function getList()
	{
		$recipes = Recipe::select('id', 'name', 'description')->with(['ingredients' => function ($q) {
			$q->select('id', 'name');
		}, 'abilities' => function ($q) {
			$q->select('id', 'name');
		}])->get();

		foreach ($recipes as $recipe) {
			foreach ($recipe->ingredients as $ingredient) {
				$code = $ingredient->codes->first();
				$ingredient->code = $code === null ? null : $code->code;
				unset($ingredient->codes);
				unset($ingredient->pivot);
			}
			foreach ($recipe->abilities as $ability) {
				$ability->value = $ability->pivot->value;
				unset($ability->pivot);
			}
			$code = $recipe->codes->first();
			$recipe->code = $code === null ? null : $code->code;
			unset($recipe->codes);
		}

		return new JsonResponse($recipes);
	}

	/**
	 * store a single Recipe (insert/update)
	 */
	public function storeRecipe(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'recipe_id' => 'nullable|integer',
			'name' => 'required|string|min:3',
			'code' => 'required|string|min:3',
			'description' => 'required|string|min:3',
			'ingredients' => 'nullable|array',
			'ingredients.*.id' => 'required|integer',
			'ingredients.*.name' => 'required|string|min:3',
			'ingredients.*.code' => 'required|string|min:3|max:8',
			'abilities' => 'nullable|array',
			'abilities.*.id' => 'required|integer',
			'abilities.*.value' => 'required|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		// recipe update
		$recipe = Recipe::find($request->recipe_id);
		if ($recipe === null) {
			$recipe = new Recipe();
			$recipe->name = $request->name;
			$recipe->description = $request->description;
			$recipe->save();
		} else {
			$recipe->name = $request->name;
			$recipe->description = $request->description;
		}
		$recipe->assignCode($request->code);
		$recipe->save();

		// manage abilities
		$collected = collect($request->abilities);
		$abilities = Ability::findMany($collected->map(function ($a) { return $a['id']; }));
		$valid_ids = $abilities->map(function ($a) { return $a->id; });

		$reduced = $collected->filter(function ($a) use ($valid_ids) {
			return $valid_ids->contains($a['id']);
		})->mapWithKeys(function ($a) {
			return [ $a['id'] => [ 'value' => $a['value'] ] ];
		});
		$recipe->abilities()->sync($reduced);

		// manage ingredients
		$ingredient_ids = [];
		foreach ($request->ingredients as $ing) {
			$ingredient = Ingredient::find($ing['id']);
			if ($ingredient === null) {
				$ingredient = new Ingredient();
				$ingredient->name = $ing['name'];
			} else {
				$ingredient->name = $ing['name'];
			}
			$ingredient->save();
			$ingredient->assignCode($ing['code']);

			$ingredient_ids[] = $ingredient->id;
		}
		$recipe->ingredients()->sync($ingredient_ids);

		return new JsonResponse([ 'success' => true ]);
	}

	/**
	 * delete a single Recipe
	 */
	public function deleteRecipe(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$recipe = Recipe::find($request->id);
		if ($recipe === null) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Recipe' ]) ], 400);
		}

		$recipe->delete();

		return new JsonResponse([ 'success' => true ]);
	}
}
