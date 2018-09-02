<?php

namespace tt2larp\Traits;

use tt2larp\Models\Code;

trait HasCodeTrait
{
	/**
	 * Code(s) referencing this Model
	 */
	public function codes()
	{
		return $this->morphMany(Code::class, 'coded');
	}

	/**
	 * helper method for associating code
	 */
	public function assignCode($code)
	{
		if ($this->code === $code) return;

		$c = Code::find($code);
		if ($c !== null) {
			$coded = $c->coded;
			if ($coded !== null) {
				$name = (new ReflectionClass($coded))->getShortName() . '(' . $coded->name . ')';
				throw new RuntimeException(__( "':code' is already assigned to :instance.", [ 'code' => $code, ':instance' => $name ] ));
			}
			$this->codes()->associate($c);
		} else {
			$this->codes()->create([ 'code' => $code ]);
		}
	}

	/**
	 * helper method for dissociating code
	 */
	public function removeCode($code)
	{
		$c = Code::find($code);
		if ($c === null) return;

		$coded = $c->coded;
		if ($coded instanceof self && $c->coded_id === $this->id) {
			$c->delete();
		}
	}
}
