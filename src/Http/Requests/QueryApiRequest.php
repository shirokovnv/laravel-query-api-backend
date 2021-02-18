<?php

namespace Shirokovnv\LaravelQueryApiBackend\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Shirokovnv\LaravelQueryApiBackend\Support\Constants;

/**
 * Class QueryApiRequest.
 */
class QueryApiRequest extends FormRequest
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
            'query_mode' => Rule::in(Constants::AVAILABLE_QUERY_MODES),
            'query_data' => 'required',
        ];
    }
}
