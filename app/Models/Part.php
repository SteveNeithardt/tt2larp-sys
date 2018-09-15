<?php

namespace tt2larp\Models;

use Illuminate\Database\Eloquent\Model;

use tt2larp\Traits\HasCodeTrait;

class Part extends Model
{
	use HasCodeTrait;

	protected $table = 'parts';

	/**
	 * Article this Part belongs to
	 */
	public function article()
	{
		return $this->belongsTo(Article::class, 'article_id', 'id');
	}

	/**
	 * Ability this Part needs
	 */
	public function ability()
	{
		return $this->belongsTo(Ability::class, 'ability_id', 'id');
	}

	/**
	 * Abilities this Part has
	 */
	public function abilities()
	{
		return $this->belongsToMany(Ability::class, 'parts_abilities', 'part_id', 'ability_id', 'id', 'id')->withPivot('value');
	}
}
