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
                function ($attribute, $value, $fail) {
                    $hasMedia = $this->has('media') && count($this->input('media', [])) > 0;

                    if (! $hasMedia) {
                        $length = strlen(trim($value ?? ''));

                        if ($length < 50) {
                            $fail(__('validation.min.string', ['min' => 50]));
                        }

                        if ($length > 350) {
                            $fail(__('validation.max.string', ['max' => 350]));
                        }
                    }
                },
            ],

            'media'   => ['required_without:content', 'array', 'min:1', 'max:10'],
            'media.*' => ['required_with:media', 'file', 'image'],
        ];
    }
}
