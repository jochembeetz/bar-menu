<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\ListCategoriesRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

final class ListCategoriesRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_validates_sort_by_field()
    {
        $request = new ListCategoriesRequest;
        $validator = Validator::make(['sortBy' => 'invalid'], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('sortBy', $validator->errors()->toArray());
    }

    public function test_it_validates_sort_order_field()
    {
        $request = new ListCategoriesRequest;
        $validator = Validator::make(['sortOrder' => 'invalid'], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('sortOrder', $validator->errors()->toArray());
    }

    public function test_it_validates_limit_field_minimum()
    {
        $request = new ListCategoriesRequest;
        $validator = Validator::make(['limit' => 0], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('limit', $validator->errors()->toArray());
    }

    public function test_it_validates_limit_field_maximum()
    {
        $request = new ListCategoriesRequest;
        $validator = Validator::make(['limit' => 101], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('limit', $validator->errors()->toArray());
    }

    public function test_it_validates_page_field_minimum()
    {
        $request = new ListCategoriesRequest;
        $validator = Validator::make(['page' => 0], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('page', $validator->errors()->toArray());
    }

    public function test_it_accepts_valid_data()
    {
        $request = new ListCategoriesRequest;
        $data = [
            'sortBy' => 'sort_order',
            'sortOrder' => 'desc',
            'limit' => 50,
            'page' => 2,
        ];

        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    public function test_it_accepts_empty_data()
    {
        $request = new ListCategoriesRequest;
        $validator = Validator::make([], $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    public function test_it_accepts_partial_data()
    {
        $request = new ListCategoriesRequest;
        $data = [
            'sortBy' => 'sort_order',
            'limit' => 25,
        ];

        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    public function test_it_accepts_valid_sort_by_values()
    {
        $request = new ListCategoriesRequest;
        $validator = Validator::make(['sortBy' => 'sort_order'], $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    public function test_it_accepts_valid_sort_order_values()
    {
        $request = new ListCategoriesRequest;

        $ascValidator = Validator::make(['sortOrder' => 'asc'], $request->rules(), $request->messages());
        $descValidator = Validator::make(['sortOrder' => 'desc'], $request->rules(), $request->messages());

        $this->assertFalse($ascValidator->fails());
        $this->assertFalse($descValidator->fails());
    }

    public function test_it_accepts_valid_limit_values()
    {
        $request = new ListCategoriesRequest;

        $minValidator = Validator::make(['limit' => 1], $request->rules(), $request->messages());
        $maxValidator = Validator::make(['limit' => 100], $request->rules(), $request->messages());
        $middleValidator = Validator::make(['limit' => 50], $request->rules(), $request->messages());

        $this->assertFalse($minValidator->fails());
        $this->assertFalse($maxValidator->fails());
        $this->assertFalse($middleValidator->fails());
    }

    public function test_it_accepts_valid_page_values()
    {
        $request = new ListCategoriesRequest;

        $minValidator = Validator::make(['page' => 1], $request->rules(), $request->messages());
        $higherValidator = Validator::make(['page' => 10], $request->rules(), $request->messages());

        $this->assertFalse($minValidator->fails());
        $this->assertFalse($higherValidator->fails());
    }
}
