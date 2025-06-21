<?php

namespace App\Validators;

class PaginationValidator
{
    /**
     * Get validation rules for pagination.
     */
    public static function rules(): array
    {
        return [
            'limit' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Get validation messages for pagination.
     */
    public static function messages(): array
    {
        return [
            'limit.integer' => 'The limit field must be an integer.',
            'limit.min' => 'The limit field must be at least 1.',
            'limit.max' => 'The limit field must be less than 100.',
            'page.integer' => 'The page field must be an integer.',
            'page.min' => 'The page field must be at least 1.',
        ];
    }

    /**
     * Get default values for pagination.
     */
    public static function defaults(): array
    {
        return [
            'limit' => 10,
            'page' => 1,
        ];
    }

    /**
     * Validate and normalize pagination parameters.
     */
    public static function normalize(array $data): array
    {
        $defaults = self::defaults();

        return [
            'limit' => $data['limit'] ?? $defaults['limit'],
            'page' => $data['page'] ?? $defaults['page'],
        ];
    }

    /**
     * Extract and normalize pagination parameters from GraphQL args.
     */
    public static function fromGraphQLArgs(array $args): array
    {
        $paginationData = [
            'limit' => $args['first'] ?? null,
            'page' => $args['page'] ?? null,
        ];

        return self::normalize($paginationData);
    }
}
