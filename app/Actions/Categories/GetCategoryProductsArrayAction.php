<?php

declare(strict_types=1);

namespace App\Actions\Categories;

use App\Models\Category;
use App\ValueObjects\CategoryFilters;

final class GetCategoryProductsArrayAction
{
    /**
     * Execute the action to get products for a category as a simple array.
     */
    public function __invoke(Category $category, CategoryFilters $filters): array
    {
        $query = $category->products()->with('ingredients');

        $this->applySorting($query, $filters);

        return $query->get()->all();
    }

    /**
     * Apply sorting to the product query within a category.
     */
    private function applySorting($query, CategoryFilters $filters): void
    {
        $query->orderBy($filters->sorting->sortBy, $filters->sorting->sortOrder);
    }
}
