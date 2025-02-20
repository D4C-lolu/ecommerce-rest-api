<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_register()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register', $userData);

        if ($response->status() !== 201) {
            echo "Response Body: " . $response->content() . PHP_EOL;
        }
        $response->assertStatus(201)
                ->assertJsonStructure(['token']);
        
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name']
        ]);
    }


    public function test_user_can_reset_password()
    {
    Mail::fake(); 
    $user = User::factory()->create();
    
    $response = $this->postJson('/api/password/reset', [
        'email' => $user->email
    ]);
    
    $response->assertStatus(200);

    $token = Password::createToken($user);
    
    $response = $this->postJson('/api/password/reset/confirm', [
        'email' => $user->email,
        'token' => $token,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123'
    ]);
    $response->assertStatus(200);
    }
}
