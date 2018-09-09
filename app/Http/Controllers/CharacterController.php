<?php

namespace tt2larp\Http\Controllers;

use Validator;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Ability;
use tt2larp\Models\Character;
use tt2larp\Models\Code;

class CharacterController extends Controller
{
	/**
	 * Main entry point for Characters, everything happens in Vuejs
	 */
	public function portal()
	{
		return view('character.portal');
	}

	/**
	 * returns all characters
	 */
	public function getList()
	{
		$characters = Character::select('id', 'name', 'player', 'description')->with(['abilities'=>function($q) {
			$q->select('id', 'name');
		}])->with('codes')->orderBy('name')->get();

		foreach ($characters as $character) {
			foreach ($character->abilities as $ability) {
				$ability->value = $ability->pivot->value;
				unset($ability->pivot);
			}
			$code = $character->codes->first();
			if ($code !== null) {
				$character->code = $character->codes->first()->code;
			} else {
				$character->code = null;
			}
			unset($character->codes);
		}

		return new JsonResponse($characters);
	}

	/**
	 * store a single character (insert/update)
	 */
	public function storeCharacter(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'id' => 'nullable|integer',
			'name' => 'required|string|min:3',
			'description' => 'nullable|string',
			'player' => 'required|string|min:3',
			'code' => 'required|string|min:3|max:8',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$character = Character::find($request->id);

		if ($character === null) $character = new Character();

		$character->name = $request->name;
		$character->description = $request->description ?? "";
		$character->player = $request->player;
		$character->save();

		$abilities = Ability::pluck('name', 'id');

		$relations_array = [];
		foreach ($request->abilities as $ab) {
			$id = $ab["id"];
			if (isset($abilities[$id]) && isset($ab["value"])) {
				$val = $ab["value"];
				if ($val > 0) {
					$val = max($val, 3);
					$relations_array[$id] = ["value" => $ab["value"]];
				}
			}
		}

		$character->abilities()->sync($relations_array);

		try {
			$character->assignCode($request->code);
		} catch (RuntimeException $e) {
			//silentreturn new JsonResponse([ 'success' => false, 'message' => $e->message ]);
		}

		return $this->getList();
	}

	/**
	 * delete a single Character
	 */
	public function deleteCharacter(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return new JsonResponse([ 'success' => false, 'errors' => $validator->errors() ], 422);
		}

		$character = Character::find($request->id);

		$character->delete();

		return new JsonResponse([ 'success' => true ]);
	}
}
