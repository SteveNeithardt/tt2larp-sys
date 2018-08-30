<?php

namespace tt2larp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use tt2larp\Models\Ability;
use tt2larp\Models\Problem;
use tt2larp\Models\Step;
use tt2larp\Models\StepNextStep;

class LibraryController extends Controller
{
	/**
	 * Main entry point for Problems, everything happens in Vuejs
	 */
	public function portal()
	{
		return view('library.portal');
	}

	/**
	 * returns all articles
	 */
	public function getList()
	{
		$articles = Article::all();

		return new JsonResponse($articles);
	}
}
