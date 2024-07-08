<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\AuthTest;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use AuthTest;

    /** @test */
    public function can_show_products()
    {

        $response = $this->getJson("/api/products");
        $response->assertStatus(200);
    }

    /** @test */
    public function can_show_a_product()
    {
        $product = Product::factory()->create();
        $response = $this->getJson("/api/product/$product->uuid");
        $response->assertStatus(200);
        $response->assertJson(['data' => ['uuid' => $product->uuid]]);;
    }


    /** @test */
    public function can_create_a_product()
    {
        $product = [
            'title' => fake()->word(),
        ];

        $response = $this->authenticatedRequest($this->getUser())->postJson('/api/product', $product);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'title' => $product['title'],
            ]
        ]);

        $this->assertDatabaseHas('products', [
            'title' => $product['title']
        ]);
    }
    /** @test */
    public function can_update_a_product()
    {
        $productUpdate = [
            'title' => fake()->word(),
        ];
        $product = Product::factory()->create();
        $response = $this->authenticatedRequest($this->getUser())->putJson("/api/product/$product->uuid", $productUpdate);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'title' => $productUpdate['title'],
            ]
        ]);
    }

    /** @test */
    public function can_delete_a_product()
    {
        ;
        $product = Product::factory()->create();
        $response = $this->authenticatedRequest($this->getUser())->deleteJson("/api/product/{$product->uuid}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }
}
