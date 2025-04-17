<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{

    use RefreshDatabase;

    public function testStore(): void
    {
        $this->artisan('migrate');

        $response = $this->post('/api/products', [
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 10,
            'description' => 'This is a test product.',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 10,
            'description' => 'This is a test product.',
        ]);
    }

    public function testShow(): void
    {
        $this->artisan('migrate');

        $product = \App\Models\Product::factory()->create();

        $response = $this->get('/api/products/' . $product->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'stock',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function testUpdate(): void
    {
        $this->artisan('migrate');

        $product = \App\Models\Product::factory()->create();

        $response = $this->put('/api/products/' . $product->id, [
            'name' => 'Updated Product',
            'price' => 150.00,
            'description' => 'This is an updated test product.',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 150.00,
            'description' => 'This is an updated test product.',
        ]);
    }

    public function testDestroy(): void
    {
        $this->artisan('migrate');

        $product = \App\Models\Product::factory()->create();

        $response = $this->delete('/api/products/' . $product->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function testValidation(): void
    {
        $this->artisan('migrate');

        $response = $this->post('/api/products', [
            'name' => '', // Invalid name
            'price' => -10, // Invalid price
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'price']);
    }
    
    public function testNotFound(): void
    {
        $this->artisan('migrate');

        $response = $this->get('/api/products/999999'); // Assuming this ID does not exist

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Product not found']);
    }
}
