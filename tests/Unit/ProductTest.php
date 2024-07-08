<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function can_create_product()
    {
        $product = Product::factory()->create([
            'title' => 'Major Product'
        ]);

        $this->assertDatabaseHas('products', [
            'title' => 'Major Product'
        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Major Product', $product->title);
    }

    public function can_update_product()
    {
        $product = Product::factory()->create();
        $product->title = 'Major Product';
        $product->save();

        $this->assertDatabaseHas('products', [
            'title' => 'Major Product'
        ]);
    }

    public function can_delete_product()
    {
        $product = Product::factory()->create();
        $product->delete();

        $this->assertDatabaseMissing('products', [
            'uuid' => $product->uuid
        ]);
    }

}
