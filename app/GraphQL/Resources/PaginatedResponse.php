<?php

declare(strict_types=1);

namespace App\GraphQL\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class PaginatedResponse
{
    /**
     * Format a LengthAwarePaginator into the standard GraphQL response structure.
     */
    public static function fromPaginator(LengthAwarePaginator $paginator): array
    {
        return [
            'data' => $paginator->items(),
            'paginatorInfo' => [
                'count' => count($paginator->items()),
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
}
