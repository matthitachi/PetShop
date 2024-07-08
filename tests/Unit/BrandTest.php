<?php

namespace Tests\Unit;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function can_create_brand()
    {
        $brand = Brand::factory()->create([
            'title' => 'Major Brand'
        ]);

        $this->assertDatabaseHas('brands', [
            'title' => 'Major Brand'
        ]);

        $this->assertInstanceOf(Brand::class, $brand);
        $this->assertEquals('Major Brand', $brand->title);
    }

    public function can_update_brand()
    {
        $brand = Brand::factory()->create();
        $brand->title = 'Major Brand';
        $brand->save();

        $this->assertDatabaseHas('brands', [
            'title' => 'Major Brand'
        ]);
    }

    public function can_delete_brand()
    {
        $brand = Brand::factory()->create();
        $brand->delete();

        $this->assertDatabaseMissing('brands', [
            'uuid' => $brand->uuid
        ]);
    }

}
