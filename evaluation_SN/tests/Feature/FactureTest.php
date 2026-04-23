<?php

namespace Tests\Feature;

use App\Models\Abonne;
use App\Models\Facture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactureTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        return $token;
    }

    public function test_can_get_all_factures(): void
    {
        $token = $this->authenticate();
        $abonne = Abonne::factory()->create();
        Facture::factory()->count(3)->create(['abonne_id' => $abonne->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/factures');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_create_facture(): void
    {
        $token = $this->authenticate();
        $abonne = Abonne::factory()->create();

        $factureData = [
            'abonne_id' => $abonne->id,
            'consommation' => 150,
            'montantTotal' => 15000,
            'dateEmission' => '2026-03-09',
            'statut' => 'Emise',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/factures', $factureData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'consommation' => 150,
                'montantTotal' => 15000,
                'statut' => 'Emise',
            ]);

        $this->assertDatabaseHas('factures', [
            'abonne_id' => $abonne->id,
            'consommation' => 150,
        ]);
    }

    public function test_cannot_create_facture_with_invalid_data(): void
    {
        $token = $this->authenticate();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/factures', [
                'abonne_id' => 999,
                'consommation' => -10,
                'statut' => 'StatutInvalide',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['abonne_id', 'consommation', 'montantTotal', 'dateEmission', 'statut']);
    }

    public function test_can_get_single_facture(): void
    {
        $token = $this->authenticate();
        $abonne = Abonne::factory()->create();
        $facture = Facture::factory()->create(['abonne_id' => $abonne->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/factures/' . $facture->id);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $facture->id,
                'consommation' => $facture->consommation,
            ]);
    }

    public function test_can_update_facture(): void
    {
        $token = $this->authenticate();
        $abonne = Abonne::factory()->create();
        $facture = Facture::factory()->create([
            'abonne_id' => $abonne->id,
            'statut' => 'Emise',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/factures/' . $facture->id, [
                'statut' => 'Payee',
            ]);

        $response->assertStatus(200)
            ->assertJson(['statut' => 'Payee']);

        $this->assertDatabaseHas('factures', [
            'id' => $facture->id,
            'statut' => 'Payee',
        ]);
    }

    public function test_can_delete_facture(): void
    {
        $token = $this->authenticate();
        $abonne = Abonne::factory()->create();
        $facture = Facture::factory()->create(['abonne_id' => $abonne->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/factures/' . $facture->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Facture supprimée avec succès']);

        $this->assertDatabaseMissing('factures', ['id' => $facture->id]);
    }

    public function test_can_get_factures_by_abonne(): void
    {
        $token = $this->authenticate();
        $abonne = Abonne::factory()->create();
        Facture::factory()->count(3)->create(['abonne_id' => $abonne->id]);
        
        $autreAbonne = Abonne::factory()->create();
        Facture::factory()->count(2)->create(['abonne_id' => $autreAbonne->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/abonne/' . $abonne->id . '/factures');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_cannot_access_factures_without_authentication(): void
    {
        $response = $this->getJson('/api/factures');
        $response->assertStatus(401);
    }

    public function test_statut_must_be_valid(): void
    {
        $token = $this->authenticate();
        $abonne = Abonne::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/factures', [
                'abonne_id' => $abonne->id,
                'consommation' => 100,
                'montantTotal' => 10000,
                'dateEmission' => '2026-03-09',
                'statut' => 'StatutInvalide',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['statut']);
    }
}
