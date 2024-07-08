<?php

namespace Tests\Feature;

use App\Models\Brand;
use Tests\AuthTest;
use Tests\TestCase;

class BrandControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use AuthTest;

    /** @test */
    public function can_show_brands()
    {

        $response = $this->getJson("/api/brands");
        $response->assertStatus(200);
    }

    /** @test */
    public function can_show_a_brand()
    {
        $brand = Brand::factory()->create();
        $response = $this->getJson("/api/brand/$brand->uuid");
        $response->assertStatus(200);
        $response->assertJson(['data' => ['uuid' => $brand->uuid]]);;
    }


    /** @test */
    public function can_create_a_brand()
    {
        $brand = [
            'title' => fake()->word(),
        ];

        $response = $this->authenticatedRequest($this->getUser())->postJson('/api/brand', $brand);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'title' => $brand['title'],
            ]
        ]);

        $this->assertDatabaseHas('brands', [
            'title' => $brand['title']
        ]);
    }
    /** @test */
    public function can_update_a_brand()
    {
        $brandUpdate = [
            'title' => fake()->word(),
        ];
        $brand = Brand::factory()->create();
        $response = $this->authenticatedRequest($this->getUser())->putJson("/api/brand/$brand->uuid", $brandUpdate);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'title' => $brandUpdate['title'],
            ]
        ]);
    }

    /** @test */
    public function can_delete_a_user()
    {
        $brand = Brand::factory()->create();
        $response = $this->authenticatedRequest($this->getUser())->deleteJson("/api/brand/{$brand->uuid}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('brands', [
            'id' => $brand->id
        ]);
    }
}
