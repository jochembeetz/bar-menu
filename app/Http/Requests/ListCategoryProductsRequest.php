<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Validators\PaginationValidator;
use App\Validators\SortingValidator;
use Illuminate\Foundation\Http\FormRequest;

final class ListCategoryProductsRequest extends FormRequest
{
    public function rules(): array
    {
        return array_merge(
            PaginationValidator::rules(),
            SortingValidator::rules(['price_in_cents'])
        );
    }

    public function messages(): array
    {
        return array_merge(
            PaginationValidator::messages(),
            SortingValidator::messages(['price_in_cents'])
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
        return (int) $this->validated('limit', 10);
    }

    public function page(): int
    {
        return (int) $this->validated('page', 1);
    }

    /**
     * Get normalized pagination parameters.
     */
    public function getPaginationParams(): array
    {
        return PaginationValidator::normalize($this->validated());
    }
}
