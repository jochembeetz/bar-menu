<?php

namespace App\GraphQL\Resolvers;

use App\GraphQL\Resources\PaginatedResponse;
use App\Validators\PaginationValidator;
use App\Validators\SortingValidator;
use GraphQL\Error\Error;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseResolver
{
    /**
     * Apply pagination and sorting to a query builder.
     */
    protected function applyPaginationAndSorting(Builder $query, array $args): array
    {
        $this->validatePaginationArgs($args);

        $paginationParams = PaginationValidator::fromGraphQLArgs($args);
        $sortingParams = SortingValidator::fromGraphQLArgs($args);

        $query->orderBy($sortingParams['column'], $sortingParams['order']);

        $paginator = $query->paginate(
            $paginationParams['limit'],
            ['*'],
            'page',
            $paginationParams['page']
        );

        return PaginatedResponse::fromPaginator($paginator);
    }

    /**
     * Apply pagination and sorting to a relationship query.
     */
    protected function applyPaginationAndSortingToRelationship($relationship, array $args): array
    {
        $this->validatePaginationArgs($args);

        $paginationParams = PaginationValidator::fromGraphQLArgs($args);
        $sortingParams = SortingValidator::fromGraphQLArgs($args);

        $relationship->orderBy($sortingParams['column'], $sortingParams['order']);

        $paginator = $relationship->paginate(
            $paginationParams['limit'],
            ['*'],
            'page',
            $paginationParams['page']
        );

        return PaginatedResponse::fromPaginator($paginator);
    }

    /**
     * Apply sorting to a relationship query and return a simple array (no pagination).
     */
    protected function applySortingToRelationship($relationship, array $args): array
    {
        $sortingParams = SortingValidator::fromGraphQLArgs($args);
        $relationship->orderBy($sortingParams['column'], $sortingParams['order']);

        return $relationship->get()->all();
    }

    /**
     * Format a LengthAwarePaginator into the standard GraphQL response structure.
     */
    protected function formatPaginatorResponse(LengthAwarePaginator $paginator): array
    {
        return PaginatedResponse::fromPaginator($paginator);
    }

    /**
     * Validate pagination arguments and throw GraphQL error if invalid.
     */
    protected function validatePaginationArgs(array $args): void
    {
        $first = $args['first'] ?? null;
        $page = $args['page'] ?? null;

        if ($first !== null) {
            if (! is_numeric($first) || (int) $first < 1) {
                throw new Error('The first field must be at least 1.');
            }
            if ((int) $first > 100) {
                throw new Error('The first field must be less than 100.');
            }
        }

        if ($page !== null) {
            if (! is_numeric($page) || (int) $page < 1) {
                throw new Error('The page field must be at least 1.');
            }
        }
    }
}
