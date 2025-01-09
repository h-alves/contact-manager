<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration(): void
    {
        $response = $this->post('/api/register', [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'test1234',
            'password_confirmation' => 'test1234',
        ]);

        $response->assertStatus(201)->assertJsonStructure(['user', 'token']);
    }
}
