<?php

namespace tt2larp\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class GetArticle extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'codes' => 'required|array',
        ];
    }

	/**
	 * Handle a failed validation attempt.
	 * @note does not redirect the url
	 *
	 * @param  \Illuminate\Contracts\Validation\Validator  $validator
	 * @return void
	 *
	 * @throws \Illuminate\Validation\ValidationException
	 */
	protected function failedValidation(Validator $validator)
	{
		//dd('fuck off');
		throw (new ValidationException($validator));
	}
}
