<?php

namespace App\Actions\Categories;

use App\Models\Category;
use App\ValueObjects\CategoryFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListCategoriesAction
{
    /**
     * Execute the action to list categories with pagination and sorting.
     */
    public function __invoke(CategoryFilters $filters): LengthAwarePaginator
    {
        $query = Category::query();

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
     * Apply sorting to the category query.
     */
    private function applySorting($query, CategoryFilters $filters): void
    {
        $query->orderBy($filters->sorting->sortBy, $filters->sorting->sortOrder);
    }
}
