<?php

namespace tt2larp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Character;

class CharacterController extends Controller
{
	/**
	 * lists all characters in a searchable interface
	 */
	public function index()
	{
		$characters = Character::with('abilities')->get();

		return view('characters.list')->with(compact('characters'));
	}

	/**
	 * view a single character
	 */
	public function view($id, Request $request)
	{
		$character = Character::find($id);

		if ($character === null) abort(422, "Character $id doesn't exist.");

		return view('characters.view')->with(compact('character'));
	}

	/**
	 * edit a single character
	 */
	public function edit($id, Request $request)
	{
		$character = Character::with('abilities')->find($id);

		return view('characters.view')->with(compact('character'));
	}

	/**
	 * store a single character (insert/update)
	 */
	public function store($id, Request $request)
	{
		abort(500, "not implmented");

		$character = Character::find($id);

		if ($character === null) $character = new Character();

		$character->save();

		return new JsonResponse(['success' => true]);
	}
}
