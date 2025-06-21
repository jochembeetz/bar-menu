<?php

declare(strict_types=1);

namespace App\ValueObjects;

final class PaginationOptions
{
    public function __construct(
        public readonly int $limit,
        public readonly int $page
    ) {
        $this->validate();
    }

    /**
     * Create from array with validation.
     */
    public static function fromArray(array $filters): self
    {
        return new self(
            limit: (int) ($filters['limit'] ?? 10),
            page: (int) ($filters['page'] ?? 1)
        );
    }

    /**
     * Create from GraphQL arguments.
     */
    public static function fromGraphQLArgs(array $args): self
    {
        return new self(
            limit: (int) ($args['first'] ?? 10),
            page: (int) ($args['page'] ?? 1)
        );
    }

    /**
     * Validate the pagination values.
     */
    private function validate(): void
    {
        if ($this->limit < 1) {
            throw new \InvalidArgumentException('Limit must be at least 1');
        }

        if ($this->limit > 100) {
            throw new \InvalidArgumentException('Limit must be at most 100');
        }

        if ($this->page < 1) {
            throw new \InvalidArgumentException('Page must be at least 1');
        }
    }
}
