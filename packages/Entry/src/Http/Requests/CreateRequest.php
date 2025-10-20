<?php

namespace Packages\Entry\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'content' => [
                'nullable',
                'string',

                function ($_attribute, $value, $fail) {
                    if (! $this->has('media') && strlen($value ?? '') < 50) {
                        $fail(__('validation.min.string', ['min' => 50]));
                    }
                },
            ],
            'media'   => ['required_without:content', 'array'],
            'media.*' => ['required_with:media', 'file', 'image'],
        ];
    }
}
