<?php

return [
	/*
    |--------------------------------------------------------------------------
	| All language strings all over the place
    |--------------------------------------------------------------------------
	*/

	// general
	'add' => 'Ajouter',
	'back' => 'Retour',
	'cancel' => 'Annuler',
	'edit' => 'Edition',
	'submit' => 'Soumettre',
	'delete' => 'Supprimer',
	'The requested :instance doesn\'t exist.' => ':instance demandé n\'existe pas.',
	':instance named \':name\' already exists.' => 'Il n\'existe pas de :instance nommé \':name\'.',

	// step_next_step
	'select type' => 'Sélectionner le type...',
	'abilities' => 'Compétences',
	'ability type' => 'compétence',
	'code type' => 'code',

	// abilities
	'Ability has :instances attached to it.' => 'La Compétence a des :instances qui lui sont attachées.',

	// characters
	'characters' => 'Personnages',
	'search' => 'Rechercher...',
	'name' => 'Nom',
	'player' => 'Joueur',

	// problems
	'problems' => 'Problèmes',
	'problem name' => 'Nom du problème',
	'edit name' => 'Editer le nom',
	'save name' => 'Sauver le nom',

	'regen graph' => 'Régénerer graph',

	'add step' => 'Ajouter une étape',
	'step description' => 'Déscription de l\'étape',
	'step name' => 'Nom de l\'étape',
	'save step' => 'Sauver l\'étape',

	'add edge' => 'Ajouter un lien',
	'save edge' => 'Sauver le lien',

	'add code' => 'Ajouter un Code',
	'add ability' => 'Ajouter une Compétence',

	'Problem is active on a ProblemStation.' => 'Le Problème est actif sur une Station.',

	// articles
	'library' => 'Bibliothèque',
	'code' => 'Code',
	'No filter' => 'Aucun filtre',

	'add part' => 'Ajouter une partie',
	'save part' => 'Sauver une partie',
	'no ability' => 'Aucune compétence',

	'This will delete \'%P%\' permanently.' => 'Ceci va supprimer \'%P%\' de manière permanente.',
	'AbilityId can\'t be null when min value is set.' => 'L\'identifiant de la compétence ne peut pas être null lorsqu\'une valeur minimale est demandée.',

	// api get article
	'More than one article present in input array.' => 'Plus d\'un article est présent dans la liste en entrée.',
	'No article present in input array.' => 'Aucune article n\'est présent dans la liste en entrée.',

	// crafting
	'crafting' => 'Atelier',
	'save recipe' => 'Sauver la recette',
	'add ingredient' => 'Ajouter un ingrédient',

	// crafting station api
	'Please enter a recipe you know.' => 'Veuillez entrer une recette que vous connaissez.',
	'Not enough ingredients for recipe.' => 'Pas assez d\'ingrédents pour cette recette.',

	// stations
	'stations' => 'Stations',
	'offline' => 'Hors ligne',
	'last activity at %A%' => 'Dernière activité à %A%',
	'Nothing to do in the library' => 'Rien à faire dans la bibliothèque.',
	'Nothing to do in the crafting station' => 'Rien à faire à l\'atelier.',
	'active problem is \'%P%\'' => 'Le problème actif courant est \'%P%\'',
	'currently on step \'%S%\'' => 'à l\'étape \'%S%\'',
	'problem is finished' => 'Problème terminé.',
	'no active problem' => 'Aucun problème actif.',
	'no problem' => 'Aucun problème',
	'cancel problem' => 'Annuler problème',
	'assign new problem' => 'Assigner un nouveau problème',
	'Are you sure?' => 'En êtes-vous certain?',
	'The Station will no longer have any active Problem.' => 'La station n\'aura plus de problème actif.',
	'The active Problem will be forcibly moved to a the new Step.' => 'Le problème actif va être transitionné à la nouvelle étape.',
	'The Station will now have an active Problem' => 'La station aura un problème actif.',
	'alert message' => 'Message d\'alerte',

	// api set station active step
	'Invalid forward value, must be non-zero.' => 'Valeur forward invalide, doit être non nulle.',

	// api try station problem
	'More than one character present in input array.' => 'Plus d\'un personnage présent dans la liste en entrée.',
	'Station :name (:id) is not a ProblemStation.' => 'La station :name (:id) n\'est pas une station à problèmes.',
	'There are no problems on this station.' => 'Il n\'y a pas de problème à cette station.',
	'Nobody is interracting with this station.' => 'Personne n\'interragis avec cette station.',

	// api crafting station
	'More than one recipe present in input array.' => 'Plus d\'une recette présente dans la liste en entrée.',
	'Station :name (:id) is not a CraftingStation.' => 'La station :name (:id) n\'est pas une station atelier.',
	'No character present in input array.' => 'Aucun personnage présent dans la liste en entrée.',
	'The Character is not allowed to craft Recipe.' => 'Le personnage n\'a pas le droit de fabriquer cette recette.',

	// command center
	'command center' => 'Centre de commande',
	'All is well' => 'Situation nominale.',

	// chat
	'chat interface' => 'Communications',
];
