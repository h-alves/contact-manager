<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token',
            ]);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'name' => 'Jane Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token',
            ]);
    }

    public function test_user_cannot_login_with_incorrect_credentials()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => 'john.doe@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => ['The provided credentials are incorrect.'],
                ],
            ]);
    }

    public function test_user_cannot_login_without_required_fields()
    {
        $data = [
            'email' => 'john.doe@example.com',
            // Falta o campo 'password'
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_user_can_logout()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123'),
        ]);

        $token = $user->createToken('TestApp')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'You have been logged out.']);
    }

    public function test_user_cannot_logout_without_authentication()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);  // Não autenticado
    }

    public function test_user_can_delete_account_with_correct_password()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'password' => 'password123',
        ];

        $token = $user->createToken('TestApp')->plainTextToken;

        $response = $this->withToken($token)->deleteJson('/api/account', $data);

        $response->assertStatus(200);
        $this->assertNull(User::find($user->id));
    }

    public function test_user_cannot_delete_account_with_incorrect_password()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'password' => 'wrongpassword',
        ];

        $token = $user->createToken('TestApp')->plainTextToken;

        $response = $this->withToken($token)->deleteJson('/api/account', $data);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => ['Senha incorreta.'],
                ],
            ]);
    }
}
