<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_contacts()
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/contact');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $contact->name]);
    }

    public function test_can_create_contact()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'John Doe',
            'cpf' => '63805808089',
            'phone' => '999999999',
            'postal_code' => '80250-104',
            'state' => 'PR',
            'city' => 'Curitiba',
            'neighborhood' => 'Batel',
            'street' => 'Rua Pauster',
            'number' => '463',
            'complement' => '14 Andar',
        ];

        $response = $this->actingAs($user)->postJson('/api/contact', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'John Doe'])
            ->assertJsonStructure(['contact', 'address']);
    }

    public function test_cannot_create_contact_invalid_address()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'John Doe',
            'cpf' => '63805808089',
            'phone' => '999999999',
            'postal_code' => '00000-000',
            'state' => 'AA',
            'city' => 'Cidade inválida',
            'neighborhood' => 'Bairro inválido',
            'street' => 'Rua inválida',
            'number' => '0000',
            'complement' => null,
        ];

        $response = $this->actingAs($user)->postJson('/api/contact', $data);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Não foi possível registrar o endereço fornecido.']);
    }

    public function test_can_show_contact()
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/contact/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $contact->name]);
    }

    public function test_cannot_show_contact_invalid_id()
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $secondUser = User::factory()->create();
        $response = $this->actingAs($secondUser)->getJson("/api/contact/{$contact->id}");

        $response->assertStatus(403)
            ->assertJson(['error' => 'Você não tem permissão de visualizar o contato.']);
    }

    public function test_can_update_contact()
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $data = [
            'name' => 'Updated Name',
            'cpf' => '63805808089',
            'phone' => '997777777'
        ];

        $response = $this->actingAs($user)->putJson("/api/contact/{$contact->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Name']);
    }

    public function test_can_delete_contact()
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/contact/{$contact->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Contato excluído com sucesso.']);

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
