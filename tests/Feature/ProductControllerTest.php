<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $token;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
        $this->category = Category::factory()->create();
    }

    protected function withAuthHeaders($headers = [])
    {
        return array_merge($headers, [
            'Authorization' => 'Bearer ' . $this->token
        ]);
    }

    public function test_can_list_products()
    {
        Product::factory()->count(5)->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/products', $this->withAuthHeaders());

        if ($response->status() !== 200) {
            echo "Response Body: " . $response->content() . PHP_EOL;
        }

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description', 
                            'category'
                        ]
                    ],
                    'meta' => [
                        'total',
                        'per_page',
                        'current_page',
                        'last_page'
                    ]
                ]);
    }

    public function test_can_create_product()
    {
        $productData = [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'stock' => 100,
            'status' => 'active',
            'sku' => $this->faker->unique()->numerify('SKU-#####'), 
            'category_id' => $this->category->id,
        ];

        $response = $this->postJson('/api/products', $productData, $this->withAuthHeaders());

        if ($response->status() !== 201) {
            echo "Response Body: " . $response->content() . PHP_EOL;
        }

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'id',
                    'name',
                    'description',
                    'stock', 
                    'status',
                    'sku',
                    'category'
                ]);

        $this->assertDatabaseHas('products', [
            'name' => $productData['name'],
            'category_id' => $this->category->id
        ]);
    }

    public function test_can_update_product()
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'stock' => 10,
            'status' => 'active',
            'sku' => 'SKU-12345'
        ]);
        
        $updateData = [
            'name' => 'Updated Product Name',
            'stock' => 50,
            'status' => 'inactive'
        ];

        $response = $this->putJson(
            "/api/products/{$product->id}",
            $updateData,
            $this->withAuthHeaders()
        );

        if ($response->status() !== 200) {
            echo "Response Body: " . $response->content() . PHP_EOL;
        }

        $response->assertStatus(200)
                ->assertJson([
                    'name' => 'Updated Product Name',
                    'stock' => 50,
                    'status' => 'inactive'
                ]);
    }

    public function test_soft_deletes_work()
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->deleteJson(
            "/api/products/{$product->id}",
            [],
            $this->withAuthHeaders()
        );

        if ($response->status() !== 200) {
            echo "Response Body: " . $response->content() . PHP_EOL;
        }

        $response->assertStatus(200)
                ->assertJson(['message' => 'Product deleted']); 
        
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_category_constraint()
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->putJson(
            "/api/products/{$product->id}",
            ['category_id' => 999], 
            $this->withAuthHeaders()
        );

        if ($response->status() !== 409) {
            echo "Response Body: " . $response->content() . PHP_EOL;
        }

        $response->assertStatus(409); 
    }

    public function test_pagination_and_sorting()
    {
        Product::factory()->create([
            'category_id' => $this->category->id,
            'name' => 'AAA Product'
        ]);
        Product::factory()->create([
            'category_id' => $this->category->id,
            'name' => 'ZZZ Product'
        ]);

        $response = $this->getJson(
            '/api/products?sort_by=name&sort_direction=desc&per_page=1',
            $this->withAuthHeaders()
        );

        if ($response->status() !== 200) {
            echo "Response Body: " . $response->content() . PHP_EOL;
        }

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'meta' => [
                        'total',
                        'per_page',
                        'current_page',
                        'last_page'
                    ]
                ])
                ->assertJsonPath('data.0.name', 'ZZZ Product')
                ->assertJsonPath('meta.per_page', 1);
    }

    public function test_search_functionality()
    {
        $searchProduct = Product::factory()->create([
            'category_id' => $this->category->id,
            'name' => 'Unique Searchable Product'
        ]);

        $response = $this->getJson(
            '/api/products/search?search=Unique',
            $this->withAuthHeaders()
        );

        if ($response->status() !== 200) {
            echo "Response Body: " . $response->content() . PHP_EOL;
        }

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Unique Searchable Product');
    }
}