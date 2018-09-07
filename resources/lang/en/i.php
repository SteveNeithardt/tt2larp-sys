<?php

return [
	/*
    |--------------------------------------------------------------------------
	| All language strings all over the place
    |--------------------------------------------------------------------------
	*/

	// general
	'add' => 'Add',
	'back' => 'Back',
	'cancel' => 'Cancel',
	'edit' => 'Edit',
	'submit' => 'Submit',
	'delete' => 'Delete',

	// step_next_step
	'select type' => 'Select type...',
	'abilities' => 'Abilities',
	'ability type' => 'ability',
	'code type' => 'code',

	// characters
	'characters' => 'Characters',
	'search' => 'Search...',
	'name' => 'Name',
	'player' => 'Player',

	// problems
	'problems' => 'Problems',
	'problem name' => 'Problem name',
	'edit name' => 'Edit name',
	'save name' => 'Save name',

	'Problem named \':name\' already exists.' => 'Problem named \':name\' already exists.',

	'regen graph' => 'Regenerate graph',

	'add step' => 'Add step',
	'step description' => 'Step description',
	'step name' => 'Step name',
	'save step' => 'Save step',

	'add edge' => 'Add link',
	'save edge' => 'Save link',

	'add code' => 'Add Code',
	'add ability' => 'Add Ability',

	// articles
	'library' => 'Library',
	'code' => 'Code',
	'edit article' => 'Edit article',

	'add part' => 'Add part',
	'save part' => 'Save part',
	'no ability' => 'No Ability',

	'Article named \':name\' already exists.' => 'Article named \':name\' already exists.',

	// api get article
	'More than one article present in input array.' => 'More than one article present in input array.',
	'No article present in input array.' => 'No article present in input array.',

	// stations
	'stations' => 'Stations',
	'offline' => 'Offline',
	'last activity at %A%' => 'last activity at %A%',
	'Nothing to do in the library' => 'Nothing to do in the library.',
	'active problem is \'%P%\'' => 'Current active Problem is \'%P%\'',
	'currently on step \'%S%\'' => 'on step \'%S%\'',
	'problem is finished' => 'Problem is finished.',
	'no active problem' => 'No active Problem.',
	'cancel problem' => 'Cancel Problem',
	'assign new problem' => 'Assign new Problem',

	// api set station active problem
	'The requested problem doesn\'t exist.' => 'The requested problem doesn\'t exist.',
	'The requested station is not a ProblemStation. Invalid Request.' => 'The requested station is not a ProblemStation. Invalid Request.',

	// api set station active step
	'The requested Step doesn\'t exist.' => 'The requested Step doesn\'t exist.',
	'Invalid forward value, must be non-zero.' => 'Invalid forward value, must be non-zero.',

	// api try station problem
	'More than one character present in input array.' => 'More than one character present in input array.',
	'Station :name (:id) is not a ProblemStation.' => 'Station :name (:id) is not a ProblemStation.',
	'There are no problems on this station.' => 'There are no problems on this station.',
	'Nobody is interracting with this station.' => 'Nobody is interracting with this station.',
];
