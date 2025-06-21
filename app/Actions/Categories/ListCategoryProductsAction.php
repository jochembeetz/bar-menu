<?php

declare(strict_types=1);

namespace App\Actions\Categories;

use App\Models\Category;
use App\ValueObjects\CategoryFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListCategoryProductsAction
{
    /**
     * Execute the action to list products for a category with pagination and sorting.
     */
    public function __invoke(Category $category, CategoryFilters $filters): LengthAwarePaginator
    {
        $query = $category->products()->with('ingredients');

        $this->applySorting($query, $filters);

        $pagination = $filters->getPagination();

        return $query->paginate(
            $pagination->limit,
            ['*'],
            'page',
            $pagination->page
        );
    }

    /**
     * Apply sorting to the product query within a category.
     */
    private function applySorting($query, CategoryFilters $filters): void
    {
        $query->orderBy($filters->sorting->sortBy, $filters->sorting->sortOrder);
    }
}
