<?php

namespace Tests\Feature;

use App\Models\Category;
use Tests\AuthTest;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use AuthTest;

    /** @test */
    public function can_show_categories()
    {

        $response = $this->getJson("/api/categories");
        $response->assertStatus(200);
    }

    /** @test */
    public function can_show_a_category()
    {
        $category = Category::factory()->create();
        $response = $this->getJson("/api/category/$category->uuid");
        $response->assertStatus(200);
        $response->assertJson(['data' => ['uuid' => $category->uuid]]);;
    }


    /** @test */
    public function can_create_a_category()
    {
        $category = [
            'title' => fake()->word(),
        ];

        $response = $this->authenticatedRequest($this->getUser())->postJson('/api/category', $category);

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
    /** @test */
    public function can_update_a_category()
    {
        $categoryUpdate = [
            'title' => fake()->word(),
        ];
        $category = Category::factory()->create();
        $response = $this->authenticatedRequest($this->getUser())->putJson("/api/category/$category->uuid", $categoryUpdate);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'title' => $categoryUpdate['title'],
            ]
        ]);
    }

    /** @test */
    public function can_delete_a_category()
    {
        ;
        $category = Category::factory()->create();
        $response = $this->authenticatedRequest($this->getUser())->deleteJson("/api/category/{$category->uuid}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }
}
