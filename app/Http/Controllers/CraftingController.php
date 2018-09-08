<?php

namespace tt2larp\Http\Controllers;

use Validator;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
		}])->get();

		foreach ($recipes as $recipe) {
			foreach ($recipe->ingredients as $ingredient) {
				$code = $ingredient->codes->first();
				$ingredient->code = $code === null ? null : $code->code;
				unset($ingredient->codes);
				unset($ingredient->pivot);
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
			'ingredients.*.code' => 'required|string|min:3',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

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

		//$ingredients = Ingredient::findMany(collect($request->ingredients)->map(function ($i) { return $i['id']; }));
		//dump($ingredients);
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
			'recipe_id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$recipe = Recipe::find($request->recipe_id);
		if ($recipe === null) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Recipe' ]) ], 400);
		}

		$recipe->delete();

		return new JsonResponse([ 'success' => true ]);
	}
//
	///**
	// * store a single Ingredient (insert/update)
	// */
	//public function storeIngredient(Request $request)
	//{
		//$validator = Validator::make($request->all(), [
			//'ingredient_id' => 'nullable|integer',
			//'recipe_id' => 'required|integer',
			//'name' => 'required|string|min:3',
			//'code' => 'required|string|min:3',
		//]);
//
		//if ($validator->fails()) {
			//return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		//}
//
		//$recipe = Recipe::find($request->recipe_id);
		//if ($recipe === null) {
			//return new JsonResponse([ 'success' => false, 'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Recipe' ]) ], 400);
		//}
//
		//$ingredient = Ingredient::find($request->ingredient_id);
		//if ($ingredient === null) {
			//$ingredient = new Ingredient()
			//$ingredient->name = $request->name;
			//$ingredient->save();
		//} else {
			//$ingredient->name = $request->name;
		//}
		//$ingredient->assignCode($request->code);
		//$ingredient->save();
//
		//$attached = false;
		//foreach ($ingredient->recipes as $r) {
			//if ($r->id === $recipe->id) {
				//$attached = true;
				//break;
			//}
		//}
		//if ($attached === false) {
			//$ingredient->recipes()->attach($recipe->id);
		//}
//
		//return new JsonResponse([ 'success' => true ]);
	//}
//
	///**
	// * delete a single Ingredient
	// */
	//public function deleteIngredient(Request $request)
	//{
		//$validator = Validator::make($request->all(), [
			//'ingredient_id' => 'required|integer',
		//]);
//
		//if ($validator->fails()) {
			//return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		//}
//
		//$ingredient = Ingredient::find($request->ingredient_id);
		//if ($ingredient === null) {
			//return new JsonResponse([ 'success' => false, 'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Ingredient' ]) ], 400);
		//}
//
		//$ingredient->recipes()->detach();
		//$ingredient->recipe()->dissociate();
		//$ingredient->delete();
//
		//return new JsonResponse([ 'success' => true ]);
	//}
}
