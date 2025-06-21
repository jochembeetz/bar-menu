<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListCategoriesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sortBy' => 'nullable|string|in:sort_order',
            'sortOrder' => 'nullable|string|in:asc,desc',
            'limit' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'sortBy.in' => 'The sortBy field must be one of the following: sort_order.',
            'sortOrder.in' => 'The sortOrder field must be one of the following: asc, desc.',
            'limit.integer' => 'The limit field must be an integer.',
            'limit.min' => 'The limit field must be at least 1.',
            'limit.max' => 'The limit field must be less than 100.',
            'page.integer' => 'The page field must be an integer.',
            'page.min' => 'The page field must be at least 1.',
        ];
    }

    public function sortBy(): string
    {
        return $this->validated('sortBy', 'sort_order');
    }

    public function sortOrder(): string
    {
        return $this->validated('sortOrder', 'asc');
    }

    public function limit(): int
    {
        return $this->validated('limit', 10);
    }

    public function page(): int
    {
        return $this->validated('page', 1);
    }
}