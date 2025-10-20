<?php

namespace Packages\Recommend\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page'     => 'sometimes|integer|min:1',
            'tab'      => 'sometimes|string',
            'tag'      => 'sometimes|string',
            'category' => 'sometimes|string',
            'article'  => 'sometimes|string',
            'entry'    => 'sometimes|string',
            'user'     => 'sometimes|string',
        ];
    }
}
