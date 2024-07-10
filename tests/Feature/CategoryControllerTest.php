<?php

namespace Tests\Feature;

use App\Models\Category;
use Tests\Traits\AuthTest;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use AuthTest;

    protected string $baseUrl = "/api/v1/category";
    #[Test]
    public function test_can_show_categories()
    {

        $response = $this->getJson("/api/v1/categories");
        $response->assertStatus(200);
    }

    #[Test]
    public function test_can_show_a_category()
    {
        $category = Category::factory()->create();
        $response = $this->getJson("$this->baseUrl/$category->uuid");
        $response->assertStatus(200);
        $response->assertJson(['data' => ['uuid' => $category->uuid]]);;
    }


    #[Test]
    public function test_can_create_a_category()
    {
        $category = [
            'title' => fake()->word(),
        ];

        $response = $this->authenticatedRequest($this->getUser())->postJson("$this->baseUrl", $category);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'title' => $category['title'],
            ]
        ]);

        $this->assertDatabaseHas('categories', [
            'title' => $category['title']
        ]);
    }
    #[Test]
    public function test_can_update_a_category()
    {
        $categoryUpdate = [
            'title' => fake()->word(),
        ];
        $category = Category::factory()->create();
        $response = $this->authenticatedRequest($this->getUser())->putJson("$this->baseUrl/$category->uuid", $categoryUpdate);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'title' => $categoryUpdate['title'],
            ]
        ]);
    }

}
