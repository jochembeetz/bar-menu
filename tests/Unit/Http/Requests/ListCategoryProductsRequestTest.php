<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\ListCategoryProductsRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class ListCategoryProductsRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_validates_sort_by_field()
    {
        $request = new ListCategoryProductsRequest();
        $validator = Validator::make(['sortBy' => 'invalid'], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('sortBy', $validator->errors()->toArray());
    }

    public function test_it_validates_sort_order_field()
    {
        $request = new ListCategoryProductsRequest();
        $validator = Validator::make(['sortOrder' => 'invalid'], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('sortOrder', $validator->errors()->toArray());
    }

    public function test_it_validates_limit_field_minimum()
    {
        $request = new ListCategoryProductsRequest();
        $validator = Validator::make(['limit' => 0], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('limit', $validator->errors()->toArray());
    }

    public function test_it_validates_limit_field_maximum()
    {
        $request = new ListCategoryProductsRequest();
        $validator = Validator::make(['limit' => 101], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('limit', $validator->errors()->toArray());
    }

    public function test_it_validates_page_field_minimum()
    {
        $request = new ListCategoryProductsRequest();
        $validator = Validator::make(['page' => 0], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('page', $validator->errors()->toArray());
    }

    public function test_it_accepts_valid_data()
    {
        $request = new ListCategoryProductsRequest();
        $data = [
            'sortBy' => 'name',
            'sortOrder' => 'desc',
            'limit' => 50,
            'page' => 2,
        ];

        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    public function test_it_accepts_empty_data()
    {
        $request = new ListCategoryProductsRequest();
        $validator = Validator::make([], $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    public function test_it_accepts_partial_data()
    {
        $request = new ListCategoryProductsRequest();
        $data = [
            'sortBy' => 'name',
            'limit' => 25,
        ];

        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    public function test_it_accepts_valid_sort_by_values()
    {
        $request = new ListCategoryProductsRequest();
        $validValues = ['sort_order', 'slug', 'created_at', 'name', 'price_in_cents'];

        foreach ($validValues as $value) {
            $validator = Validator::make(['sortBy' => $value], $request->rules(), $request->messages());
            $this->assertFalse($validator->fails(), "Validation should pass for sortBy: {$value}");
        }
    }

    public function test_it_accepts_valid_sort_order_values()
    {
        $request = new ListCategoryProductsRequest();

        $ascValidator = Validator::make(['sortOrder' => 'asc'], $request->rules(), $request->messages());
        $descValidator = Validator::make(['sortOrder' => 'desc'], $request->rules(), $request->messages());

        $this->assertFalse($ascValidator->fails());
        $this->assertFalse($descValidator->fails());
    }

    public function test_it_accepts_valid_limit_values()
    {
        $request = new ListCategoryProductsRequest();

        $minValidator = Validator::make(['limit' => 1], $request->rules(), $request->messages());
        $maxValidator = Validator::make(['limit' => 100], $request->rules(), $request->messages());
        $middleValidator = Validator::make(['limit' => 50], $request->rules(), $request->messages());

        $this->assertFalse($minValidator->fails());
        $this->assertFalse($maxValidator->fails());
        $this->assertFalse($middleValidator->fails());
    }

    public function test_it_accepts_valid_page_values()
    {
        $request = new ListCategoryProductsRequest();

        $minValidator = Validator::make(['page' => 1], $request->rules(), $request->messages());
        $higherValidator = Validator::make(['page' => 10], $request->rules(), $request->messages());

        $this->assertFalse($minValidator->fails());
        $this->assertFalse($higherValidator->fails());
    }

    public function test_it_validates_limit_as_string()
    {
        $request = new ListCategoryProductsRequest();
        $validator = Validator::make(['limit' => 'abc'], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('limit', $validator->errors()->toArray());
    }

    public function test_it_validates_page_as_string()
    {
        $request = new ListCategoryProductsRequest();
        $validator = Validator::make(['page' => 'abc'], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('page', $validator->errors()->toArray());
    }

    public function test_it_validates_sort_by_as_integer()
    {
        $request = new ListCategoryProductsRequest();
        $validator = Validator::make(['sortBy' => 123], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('sortBy', $validator->errors()->toArray());
    }

    public function test_it_validates_sort_order_as_integer()
    {
        $request = new ListCategoryProductsRequest();
        $validator = Validator::make(['sortOrder' => 123], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('sortOrder', $validator->errors()->toArray());
    }
}
