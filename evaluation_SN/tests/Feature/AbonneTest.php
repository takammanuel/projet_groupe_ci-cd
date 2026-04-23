<?php

namespace Tests\Feature;

use App\Models\Abonne;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbonneTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        return $token;
    }

    public function test_can_get_all_abonnes(): void
    {
        $token = $this->authenticate();
        Abonne::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/abonne');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    public function test_can_create_abonne(): void
    {
        $token = $this->authenticate();

        $abonneData = [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'ville' => 'Douala',
            'quartier' => 'Akwa',
            'numeroCompteur' => 'TEST001',
            'typeAbonnement' => 'Domestique',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/abonne', $abonneData);

        $response->assertStatus(201)
            ->assertJson($abonneData);

        $this->assertDatabaseHas('abonnes', $abonneData);
    }

    public function test_cannot_create_abonne_with_invalid_data(): void
    {
        $token = $this->authenticate();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/abonne', [
                'nom' => '',
                'ville' => 'VilleInvalide',
                'typeAbonnement' => 'TypeInvalide',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nom', 'prenom', 'ville', 'numeroCompteur', 'typeAbonnement']);
    }

    public function test_can_get_single_abonne(): void
    {
        $token = $this->authenticate();
        $abonne = Abonne::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/abonne/' . $abonne->id);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $abonne->id,
                'nom' => $abonne->nom,
            ]);
    }

    public function test_can_update_abonne(): void
    {
        $token = $this->authenticate();
        $abonne = Abonne::factory()->create();

        $updateData = [
            'nom' => 'Nouveau Nom',
            'prenom' => 'Nouveau Prenom',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/abonne/' . $abonne->id, array_merge($abonne->toArray(), $updateData));

        $response->assertStatus(200)
            ->assertJson($updateData);

        $this->assertDatabaseHas('abonnes', $updateData);
    }

    public function test_can_delete_abonne(): void
    {
        $token = $this->authenticate();
        $abonne = Abonne::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/abonne/' . $abonne->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('abonnes', ['id' => $abonne->id]);
    }

    public function test_cannot_access_abonnes_without_authentication(): void
    {
        $response = $this->getJson('/api/abonne');
        $response->assertStatus(401);
    }

    public function test_numero_compteur_must_be_unique(): void
    {
        $token = $this->authenticate();
        $abonne = Abonne::factory()->create(['numeroCompteur' => 'UNIQUE001']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/abonne', [
                'nom' => 'Test',
                'prenom' => 'User',
                'ville' => 'Douala',
                'quartier' => 'Akwa',
                'numeroCompteur' => 'UNIQUE001',
                'typeAbonnement' => 'Domestique',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['numeroCompteur']);
    }
}
