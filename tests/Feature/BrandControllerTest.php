<?php

namespace Tests\Feature;

use App\Models\Brand;
use Tests\TestCase;
use Tests\Traits\AuthTest;

class BrandControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use AuthTest;

    protected string $baseUrl = '/api/v1/brand';

    #[Test]
    public function test_can_show_brands()
    {

        $response = $this->getJson('/api/v1/brands');
        $response->assertStatus(200);
    }

    #[Test]
    public function test_can_show_a_brand()
    {
        $brand = Brand::factory()->create();
        $response = $this->getJson("$this->baseUrl/$brand->uuid");
        $response->assertStatus(200);
        $response->assertJson(['data' => ['uuid' => $brand->uuid]]);
    }

    #[Test]
    public function test_can_create_a_brand()
    {
        $brand = [
            'title' => fake()->word(),
        ];

        $response = $this->authenticatedRequest($this->getUser())->postJson("$this->baseUrl", $brand);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'title' => $brand['title'],
            ],
        ]);

        $this->assertDatabaseHas('brands', [
            'title' => $brand['title'],
        ]);
    }

    #[Test]
    public function test_can_update_a_brand()
    {
        $brandUpdate = [
            'title' => fake()->word(),
        ];
        $brand = Brand::factory()->create();
        $response = $this->authenticatedRequest($this->getUser())->putJson("$this->baseUrl/$brand->uuid", $brandUpdate);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'title' => $brandUpdate['title'],
            ],
        ]);
    }

    #[Test]
    public function test_can_delete_a_brand()
    {
        $brand = Brand::factory()->create();
        $response = $this->authenticatedRequest($this->getUser())->deleteJson("$this->baseUrl/{$brand->uuid}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('brands', [
            'uuid' => $brand->uuid,
        ]);
    }
}
