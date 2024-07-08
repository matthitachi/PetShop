<?php

namespace Tests\Unit;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function can_create_category()
    {
        $category = Category::factory()->create([
            'title' => 'Major Category'
        ]);

        $this->assertDatabaseHas('categories', [
            'title' => 'Major Category'
        ]);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Major Category', $category->title);
    }

    public function can_update_category()
    {
        $category = Category::factory()->create();
        $category->title = 'Major Category';
        $category->save();

        $this->assertDatabaseHas('categories', [
            'title' => 'Major Category'
        ]);
    }

    public function can_delete_category()
    {
        $category = Category::factory()->create();
        $category->delete();

        $this->assertDatabaseMissing('categories', [
            'uuid' => $category->uuid
        ]);
    }

}
