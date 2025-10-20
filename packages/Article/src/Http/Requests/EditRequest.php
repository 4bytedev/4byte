<?php

namespace Packages\Article\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Packages\Article\Models\Article;

class EditRequest extends FormRequest
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
        $isDraft = ! $this->boolean('published', false);

        $rules = [
            'title' => ['required', 'string', 'min:10'],
        ];

        if (! $isDraft) {
            $rules = array_merge($rules, [
                'excerpt'      => ['required', 'string', 'min:100'],
                'content'      => ['required', 'string', 'min:500'],
                'categories'   => ['required', 'array', 'min:1', 'max:3'],
                'categories.*' => ['string'],
                'tags'         => ['required', 'array', 'min:1', 'max:3'],
                'tags.*'       => ['string'],
                'image'        => ['required', 'file', 'image'],
                'sources'      => ['required', 'array', 'min:1'],
                'sources.url'  => ['required', 'string', 'url'],
                'sources.date' => ['required', 'date'],
            ]);
        }

        return $rules;
    }

    public function createSlug(?int $ignoreId = null): string
    {
        $title    = $this->input('title');
        $baseSlug = Str::slug($title);
        $slug     = $baseSlug;
        $counter  = 1;

        while (
            Article::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
