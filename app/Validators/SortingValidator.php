<?php

namespace App\Validators;

class SortingValidator
{
    /**
     * Get validation rules for sorting.
     */
    public static function rules(array $allowedSortFields = []): array
    {
        $defaultSortFields = ['sort_order', 'slug', 'created_at', 'name'];
        $sortFields = array_merge($defaultSortFields, $allowedSortFields);

        return [
            'sortBy' => 'nullable|string|in:'.implode(',', $sortFields),
            'sortOrder' => 'nullable|string|in:asc,desc',
        ];
    }

    /**
     * Get validation messages for sorting.
     */
    public static function messages(array $allowedSortFields = []): array
    {
        $defaultSortFields = ['sort_order', 'slug', 'created_at', 'name'];
        $sortFields = array_merge($defaultSortFields, $allowedSortFields);

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
     * Extract sorting parameters from GraphQL args.
     */
    public static function fromGraphQLArgs(array $args): array
    {
        return [
            'column' => $args['orderBy']['column'] ?? 'sort_order',
            'order' => $args['orderBy']['order'] ?? 'asc',
        ];
    }
}
