<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'description',
                            'price',
                            'stock_quantity',
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
            'price' => $this->faker->randomFloat(2, 10, 100),
            'stock_quantity' => 100,
            'category_id' => $this->category->id,
        ];

        $response = $this->postJson('/api/products', $productData, $this->withAuthHeaders());
        
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'id',
                    'name',
                    'slug',
                    'description',
                    'price',
                    'stock_quantity',
                    'category_id'
                ]);

        $this->assertDatabaseHas('products', [
            'name' => $productData['name'],
            'slug' => Str::slug($productData['name']),
            'category_id' => $this->category->id
        ]);
    }

    public function test_can_update_product()
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'price' => 50.00,
            'stock_quantity' => 10
        ]);
        
        $updateData = [
            'name' => 'Updated Product Name',
            'price' => 199.99,
            'stock_quantity' => 50,
            'category_id' => $this->category->id
        ];

        $response = $this->putJson(
            "/api/products/{$product->id}",
            $updateData,
            $this->withAuthHeaders()
        );

        $response->assertStatus(200)
                ->assertJson([
                    'name' => 'Updated Product Name',
                    'slug' => 'updated-product-name',
                    'price' => 199.99,
                    'stock_quantity' => 50
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


        $response->assertStatus(200)
                ->assertJson(['message' => 'Product deleted']);
        
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_category_constraint()
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->putJson(
            "/api/products/{$product->id}",
            ['category_id' => 999], // Non-existent category
            $this->withAuthHeaders()
        );
    
        $response->assertStatus(422);  
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
    
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data',
                     'total', 
                     'per_page',
                     'current_page',
                     'last_page'
                 ])
                 ->assertJsonPath('data.0.name', 'ZZZ Product')
                 ->assertJsonPath('per_page', 1); 
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

        
        //echo "Response Body (search): " . $response->content() . PHP_EOL;
        

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Unique Searchable Product');
    }
}