<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\AuthTest;

class ProductControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use AuthTest;

    protected string $baseUrl = '/api/v1/product';

    #[Test]
    public function test_can_show_products()
    {

        $response = $this->getJson('/api/v1/products');
        $response->assertStatus(200);
    }
}
