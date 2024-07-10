<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\File;
use App\Models\Product;
use Tests\Traits\AuthTest;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use AuthTest;

    protected string $baseUrl = "/api/v1/product";

    #[Test]
    public function test_can_show_products()
    {

        $response = $this->getJson("/api/v1/products");
        $response->assertStatus(200);
    }

}
