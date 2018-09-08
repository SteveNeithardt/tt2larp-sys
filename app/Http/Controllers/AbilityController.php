<?php

namespace tt2larp\Http\Controllers;

use Validator;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Ability;

class AbilityController extends Controller
{
	/**
	 * Main entry point for Abilities, everything happens in Vuejs
	 */
	public function portal()
	{
		$abilities = Ability::select('id', 'name')->orderBy('name')->get();

		return view('ability.portal')->with(compact('abilities'));
	}

	/**
	 * returns all abilities
	 */
	public function getList()
	{
		$abilities = Ability::select('id', 'name')->orderBy('name')->get();

		foreach ($abilities as $ability) {
			$ability->value = 0;
		}

		return new JsonResponse($abilities);
	}

	/**
	 * store a single Ability (insert/update)
	 */
	public function storeAbility(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'id' => 'sometimes|integer',
			'name' => 'required|string|min:3',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$ability = Ability::find($request->id);

		if ($ability === null) $ability = new Ability();

		$ability->name = $request->name;

		$ability->save();

		return new JsonResponse([ 'success' => true ]);
	}

	/**
	 * delete a single Ability
	 */
	public function deleteAbility(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$ability = Ability::find($request->id);

		if ($ability === null) {
			return new JsonResponse([ 'success' => true, 'message' => __('i.The requested :instance doesn\'t exist.', [ 'instance' => 'Ability' ]) ], 400);
		}

		if ($ability->characters()->exists()) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.Ability has :instances attached to it.', [ 'instances' => 'Characters' ]) ], 422);
		}

		if ($ability->stepNextSteps()->exists()) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.Ability has :instances attached to it.', [ 'instances' => 'StepNextSteps' ]) ], 422);
		}

		if ($ability->recipes()->exists()) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.Ability has :instances attached to it.', [ 'instances' => 'Recipes' ]) ], 422);
		}

		if ($ability->parts()->exists()) {
			return new JsonResponse([ 'success' => false, 'message' => __('i.Ability has :instances attached to it.', [ 'instances' => 'Parts' ]) ], 422);
		}

		$ability->delete();

		return new JsonResponse([ 'success' => true ]);
	}
}
