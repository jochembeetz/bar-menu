<?php

namespace App\GraphQL\Resolvers;

use App\Validators\PaginationValidator;
use App\Validators\SortingValidator;
use GraphQL\Error\Error;
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

        return [
            'data' => $paginator->items(),
            'paginatorInfo' => [
                'count' => $paginator->count(),
                'currentPage' => $paginator->currentPage(),
                'firstItem' => $paginator->firstItem(),
                'hasMorePages' => $paginator->hasMorePages(),
                'lastItem' => $paginator->lastItem(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
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

        return [
            'data' => $paginator->items(),
            'paginatorInfo' => [
                'count' => $paginator->count(),
                'currentPage' => $paginator->currentPage(),
                'firstItem' => $paginator->firstItem(),
                'hasMorePages' => $paginator->hasMorePages(),
                'lastItem' => $paginator->lastItem(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
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
     * Validate pagination arguments and throw GraphQL error if invalid.
     */
    protected function validatePaginationArgs(array $args): void
    {
        $first = $args['first'] ?? null;
        $page = $args['page'] ?? null;

        if ($first !== null) {
            if (!is_numeric($first) || (int) $first < 1) {
                throw new Error('The first field must be at least 1.');
            }
            if ((int) $first > 100) {
                throw new Error('The first field must be less than 100.');
            }
        }

        if ($page !== null) {
            if (!is_numeric($page) || (int) $page < 1) {
                throw new Error('The page field must be at least 1.');
            }
        }
    }
}
