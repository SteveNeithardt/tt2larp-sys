<?php

namespace tt2larp\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

	/**
	 * Show the command terminal.
	 */
	public function command()
	{
		$title = config('app.name', 'Laravel') . ' - ' . __('i.command center');

		return view('command')->with(compact('title'));
	}
}
