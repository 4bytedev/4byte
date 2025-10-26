<?php

namespace Packages\React\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

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
        $serviceClass = config('react.callbacks')[$type] ?? null;
        $baseClass    = config('react.classes')[$type] ?? null;

        if (! isset($serviceClass) || ! isset($baseClass)) {
            throw ValidationException::withMessages(['type' => 'Invalid reaction type.']);
        }

        $service = $serviceClass === 'self' ? null : app($serviceClass);
        $itemId  = $serviceClass === 'self'
            ? $this->route('slug')
            : $service->getId($this->slug);

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
