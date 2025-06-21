<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListCategoryProductsRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Services\CategoryService;
use App\ValueObjects\CategoryFilters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Get(
 *     path="/categories/{categoryId}/products",
 *     summary="List products in a category",
 *     tags={"Categories"},
 *
 *     @OA\Parameter(
 *         name="categoryId",
 *         in="path",
 *         description="Category ID",
 *         required=true,
 *
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Parameter(
 *         name="sortBy",
 *         in="query",
 *         description="Sort by field",
 *         required=false,
 *
 *         @OA\Schema(type="string", enum={"sort_order", "slug", "created_at", "name", "price_in_cents"})
 *     ),
 *
 *     @OA\Parameter(
 *         name="sortOrder",
 *         in="query",
 *         description="Sort order",
 *         required=false,
 *
 *         @OA\Schema(type="string", enum={"asc", "desc"})
 *     ),
 *
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="Limit",
 *         required=false,
 *
 *         @OA\Schema(type="integer", default=10)
 *     ),
 *
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page",
 *         required=false,
 *
 *         @OA\Schema(type="integer", default=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="List of products in the category",
 *
 *         @OA\JsonContent(
 *             type="object",
 *
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *
 *                 @OA\Items(
 *                     type="object",
 *
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="slug", type="string"),
 *                     @OA\Property(property="description", type="string", nullable=true),
 *                     @OA\Property(property="price_in_cents", type="integer"),
 *                     @OA\Property(
 *                         property="ingredients",
 *                         type="array",
 *
 *                         @OA\Items(
 *                             type="object",
 *
 *                             @OA\Property(property="id", type="integer"),
 *                             @OA\Property(property="name", type="string"),
 *                             @OA\Property(property="slug", type="string"),
 *                             @OA\Property(property="description", type="string", nullable=true),
 *                             @OA\Property(property="type", type="string", enum={"base", "optional", "add-on"})
 *                         )
 *                     )
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="links",
 *                 type="object",
 *                 @OA\Property(property="first", type="string"),
 *                 @OA\Property(property="last", type="string"),
 *                 @OA\Property(property="prev", type="string", nullable=true),
 *                 @OA\Property(property="next", type="string", nullable=true)
 *             ),
 *             @OA\Property(
 *                 property="meta",
 *                 type="object",
 *                 @OA\Property(property="current_page", type="integer"),
 *                 @OA\Property(property="from", type="integer"),
 *                 @OA\Property(property="last_page", type="integer"),
 *                 @OA\Property(property="per_page", type="integer"),
 *                 @OA\Property(property="to", type="integer"),
 *                 @OA\Property(property="total", type="integer"),
 *                 @OA\Property(
 *                     property="links",
 *                     type="array",
 *
 *                     @OA\Items(
 *                         type="object",
 *
 *                         @OA\Property(property="url", type="string", nullable=true),
 *                         @OA\Property(property="label", type="string"),
 *                         @OA\Property(property="active", type="boolean")
 *                     )
 *                 ),
 *                 @OA\Property(property="path", type="string")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Category not found"
 *     )
 * )
 */
class ListCategoryProductsController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    public function __invoke(ListCategoryProductsRequest $request, Category $category): AnonymousResourceCollection|JsonResponse
    {
        $filters = CategoryFilters::withPagination([
            'sortBy' => $request->sortBy(),
            'sortOrder' => $request->sortOrder(),
            'limit' => $request->limit(),
            'page' => $request->page(),
        ]);

        $products = $this->categoryService->getCategoryProducts($category, $filters);

        return ProductResource::collection($products);
    }
}
