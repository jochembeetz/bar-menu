<?php

declare(strict_types=1);

namespace App\Validators;

final class SortingValidator
{
    /**
     * Get validation rules for sorting.
     */
    public static function rules(array $extraAllowedSortFields = []): array
    {
        $defaultSortFields = ['sort_order', 'slug', 'created_at', 'name'];
        $sortFields = array_merge($defaultSortFields, $extraAllowedSortFields);

        return [
            'sortBy' => 'nullable|string|in:'.implode(',', $sortFields),
            'sortOrder' => 'nullable|string|in:asc,desc',
        ];
    }

    /**
     * Get validation messages for sorting.
     */
    public static function messages(array $extraAllowedSortFields = []): array
    {
        $defaultSortFields = ['sort_order', 'slug', 'created_at', 'name'];
        $sortFields = array_merge($defaultSortFields, $extraAllowedSortFields);

        return [
            'sortBy.in' => 'The sortBy field must be one of the following: '.implode(', ', $sortFields).'.',
            'sortOrder.in' => 'The sortOrder field must be one of the following: asc, desc.',
        ];
    }

    /**
     * Get default values for sorting.
     */
    public static function defaults(): array
    {
        return [
            'column' => 'sort_order',
            'order' => 'asc',
        ];
    }

    /**
     * Extract and validate sorting parameters from GraphQL args.
     */
    public static function fromGraphQLArgs(array $args, array $extraAllowedSortFields = []): array
    {
        $defaultSortFields = ['sort_order', 'slug', 'created_at', 'name'];
        $sortFields = array_merge($defaultSortFields, $extraAllowedSortFields);

        $column = $args['orderBy']['column'] ?? 'sort_order';
        $order = $args['orderBy']['order'] ?? 'asc';

        // Validate column name to prevent SQL injection
        if (! in_array($column, $sortFields)) {
            throw new \InvalidArgumentException(
                'Invalid sort column. Allowed columns: '.implode(', ', $sortFields)
            );
        }

        // Validate order direction
        if (! in_array(strtolower($order), ['asc', 'desc'])) {
            throw new \InvalidArgumentException('Sort order must be "asc" or "desc"');
        }

        return [
            'column' => $column,
            'order' => strtolower($order),
        ];
    }
}
