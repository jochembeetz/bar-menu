<?php

namespace App\ValueObjects;

class CategoryFilters
{
    public function __construct(
        public readonly SortingOptions $sorting,
        public readonly ?PaginationOptions $pagination = null
    ) {}

    /**
     * Create with both sorting and pagination.
     */
    public static function withPagination(array $filters): self
    {
        return new self(
            sorting: SortingOptions::fromArray($filters),
            pagination: PaginationOptions::fromArray($filters)
        );
    }

    /**
     * Create with sorting only (no pagination).
     */
    public static function sortingOnly(array $filters): self
    {
        return new self(
            sorting: SortingOptions::fromArray($filters)
        );
    }

    /**
     * Create from GraphQL arguments with pagination.
     */
    public static function fromGraphQLArgs(array $args): self
    {
        return new self(
            sorting: SortingOptions::fromGraphQLArgs($args),
            pagination: PaginationOptions::fromGraphQLArgs($args)
        );
    }

    /**
     * Create from GraphQL arguments for sorting only.
     */
    public static function fromGraphQLArgsSortingOnly(array $args): self
    {
        return new self(
            sorting: SortingOptions::fromGraphQLArgs($args)
        );
    }

    /**
     * Check if pagination is enabled.
     */
    public function hasPagination(): bool
    {
        return $this->pagination !== null;
    }

    /**
     * Get pagination options (throws if not available).
     */
    public function getPagination(): PaginationOptions
    {
        if (! $this->hasPagination()) {
            throw new \InvalidArgumentException('Pagination options not available');
        }

        return $this->pagination;
    }

    /**
     * Convert to array for backward compatibility.
     */
    public function toArray(): array
    {
        $result = [
            'sortBy' => $this->sorting->sortBy,
            'sortOrder' => $this->sorting->sortOrder,
        ];

        if ($this->hasPagination()) {
            $result['limit'] = $this->pagination->limit;
            $result['page'] = $this->pagination->page;
        }

        return $result;
    }
}
