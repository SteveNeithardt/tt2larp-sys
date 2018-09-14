<?php

namespace tt2larp\Traits;

use ReflectionClass;
use RuntimeException;

use tt2larp\Models\Code;

trait HasCodeTrait
{
	/**
	 * Code(s) referencing this Model
	 */
	public function codes()
	//public function code()//@TODO @investigate
	{
		return $this->morphMany(Code::class, 'coded');
		//return $this->morphOne(Code::class, 'coded');//@TODO @investigate
	}

	/**
	 * get code attribute
	 */
	//public function getCodeAttribute()
	//{
		//$code = $this->codes()->first();
		//return $code === null ? null : $code->code;
	//}

	/**
	 * helper method for associating code
	 *
	 * @param  $code
	 * @return void
	 *
	 * @throws \RuntimeException
	 */
	public function assignCode($code)
	{
		$local = $this->codes;
		foreach ($local as $c) {
			if ($c->code === $code) return;
		}

		$this->codes()->create([ 'code' => $code ]);

		foreach ($local as $l) {
			$l->delete();
		}
	}

	/**
	 * helper method for dissociating code
	 */
	public function removeCode($code)
	{
		$codes = Code::where('code', $code)->get();

		foreach ($codes as $c) {
			$coded = $c->coded;
			if ($coded instanceof self && $c->coded_id === $this->id) {
				$c->delete();
			}
		}
	}
}
