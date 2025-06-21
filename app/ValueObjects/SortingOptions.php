<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Support\Str;

final class SortingOptions
{
    public function __construct(
        public readonly string $sortBy,
        public readonly string $sortOrder
    ) {
        $this->validate();
    }

    /**
     * Create from array with validation.
     */
    public static function fromArray(array $filters): self
    {
        return new self(
            sortBy: $filters['sortBy'] ?? 'sort_order',
            sortOrder: $filters['sortOrder'] ?? 'asc'
        );
    }

    /**
     * Create from GraphQL arguments.
     */
    public static function fromGraphQLArgs(array $args): self
    {
        return new self(
            sortBy: $args['orderBy']['column'] ?? 'sort_order',
            sortOrder: $args['orderBy']['order'] ?? 'asc'
        );
    }

    /**
     * Validate the sorting values.
     */
    private function validate(): void
    {
        if (! in_array(Str::lower($this->sortOrder), ['asc', 'desc'])) {
            throw new \InvalidArgumentException('Sort order must be "asc" or "desc"');
        }
    }
}
