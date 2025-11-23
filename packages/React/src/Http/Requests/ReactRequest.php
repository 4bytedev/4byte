<?php

namespace Packages\React\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Packages\React\Services\ReactService;

class ReactRequest extends FormRequest
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
            'type' => 'required|string',
            'slug' => 'required|string',
        ];
    }

    /**
     * Resolve target model.
     *
     * @return array<int|mixed|string|null>
     */
    public function resolveTarget(): array
    {
        $type         = $this->route('type');
        $callback = ReactService::getCallback($type);
        $baseClass    = ReactService::getClass($type);

        if (! isset($callback) || ! isset($baseClass)) {
            throw ValidationException::withMessages(['type' => 'Invalid reaction type.']);
        }

        $itemId = $callback($this->route('slug'));

        return [$baseClass, $itemId, $type];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => $this->route('type'),
            'slug' => $this->route('slug'),
        ]);
    }
}
