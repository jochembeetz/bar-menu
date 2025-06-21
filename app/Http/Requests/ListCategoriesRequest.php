<?php

namespace App\Http\Requests;

use App\Validators\PaginationValidator;
use App\Validators\SortingValidator;
use Illuminate\Foundation\Http\FormRequest;

class ListCategoriesRequest extends FormRequest
{
    public function rules(): array
    {
        return array_merge(
            PaginationValidator::rules(),
            SortingValidator::rules()
        );
    }

    public function messages(): array
    {
        return array_merge(
            PaginationValidator::messages(),
            SortingValidator::messages()
        );
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

    /**
     * Get normalized pagination parameters.
     */
    public function getPaginationParams(): array
    {
        return PaginationValidator::normalize($this->validated());
    }
}
