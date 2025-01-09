<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CPFTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_cpf() {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/contact', [
            'name' => 'Test User',
            'cpf' => 47454771041,
            'phone' => '41955555555',
            'cep' => '80250104',
            'uf' => 'Paraná',
            'cidade' => 'Curitiba',
            'bairro' => 'Batel',
            'rua' => 'Pasteur',
            'numero' => '463',
            'complemento' => '',
        ]);

        $response->assertStatus(201)->assertJsonStructure(['contact']);
    }

    public function test_invalid_cpf() {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/contact', [
            'name' => 'Test User',
            'cpf' => 12345678900,
            'phone' => '41955555555',
            'cep' => '80250104',
            'uf' => 'Paraná',
            'cidade' => 'Curitiba',
            'bairro' => 'Batel',
            'rua' => 'Pasteur',
            'numero' => '463',
            'complemento' => '',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['cpf']);
    }
}
