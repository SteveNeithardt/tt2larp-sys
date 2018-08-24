<?php

namespace tt2larp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Ability;

class AbilityController extends Controller
{
	/**
	 * lists all abilities in a searchable interface
	 */
	public function index()
	{
		$abilities = Ability::all();

		return view('ability.list')->with(compact('abilities'));
	}

	/**
	 * view a single ability
	 */
	public function view($id, Request $request)
	{
		$ability = ability::find($id);

		if ($ability === null) abort(422, "Ability $id doesn't exist.");

		return view('ability.view')->with(compact('ability'));
	}

	/**
	 * edit a single ability
	 */
	public function edit($id, Request $request)
	{
		$ability = Ability::with('abilities')->find($id);

		return view('ability.view')->with(compact('ability'));
	}

	/**
	 * store a single ability (insert/update)
	 */
	public function store($id, Request $request)
	{
		abort(500, "not implmented");

		$ability = Ability::find($id);

		if ($ability === null) $ability = new Ability();

		$ability->save();

		return new JsonResponse(['success' => true]);
	}
}
