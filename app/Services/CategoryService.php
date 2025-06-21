<?php

namespace App\Services;

use App\Actions\Categories\GetCategoryProductsArrayAction;
use App\Actions\Categories\ListCategoriesAction;
use App\Actions\Categories\ListCategoryProductsAction;
use App\Models\Category;
use App\ValueObjects\CategoryFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function __construct(
        private readonly ListCategoriesAction $listCategoriesAction,
        private readonly ListCategoryProductsAction $listCategoryProductsAction,
        private readonly GetCategoryProductsArrayAction $getCategoryProductsArrayAction
    ) {}

    /**
     * Get paginated categories with sorting.
     */
    public function getPaginatedCategories(CategoryFilters $filters): LengthAwarePaginator
    {
        return ($this->listCategoriesAction)($filters);
    }

    /**
     * Get paginated products for a specific category.
     */
    public function getCategoryProducts(Category $category, CategoryFilters $filters): LengthAwarePaginator
    {
        return ($this->listCategoryProductsAction)($category, $filters);
    }

    /**
     * Get all products for a category (simple array, no pagination).
     */
    public function getCategoryProductsArray(Category $category, CategoryFilters $filters): array
    {
        return ($this->getCategoryProductsArrayAction)($category, $filters);
    }
}
