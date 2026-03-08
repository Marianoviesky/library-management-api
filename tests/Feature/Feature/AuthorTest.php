<?php

namespace Tests\Feature\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Author;

class AuthorTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase; // Réinitialise la BDD temporaire avant chaque test
    
    public function test_unauthenticated_user_cannot_create_author()
    {
        $response = $this->postJson('/api/v1/authors', [
            'name' => 'Victor Hugo'
        ]);

        $response->assertStatus(401); // 401 = Non Autorisé (Unauthenticated)
    }

    public function test_authenticated_user_can_create_author()
    {
        $user = User::factory()->create(); // Crée un faux utilisateur

        // actingAs($user) simule qu'on est connecté avec cet utilisateur
        $response = $this->actingAs($user)->postJson('/api/v1/authors', [
            'name' => 'Victor Hugo',
            'bio' => 'Auteur français'
        ]);

        $response->assertStatus(201) // 201 = Créé avec succès
                 ->assertJsonFragment(['name' => 'Victor Hugo']); // Vérifie que le JSON de retour est correct
    }

    public function test_author_creation_requires_a_name()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/authors', [
            'bio' => 'Pas de nom...'
        ]);

        $response->assertStatus(422) // 422 = Erreur de validation
                 ->assertJsonStructure(['message', 'errors' => ['name']]);
    }
}
