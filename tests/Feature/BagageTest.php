<?php

namespace Tests\Feature;

use App\Models\Bagage;
use App\Models\User;
use Tests\TestCase;

class BagageTest extends TestCase
{
    public function test_authenticated_user_can_view_bagages_index(): void
    {
        $user = User::where('login', 'admin')->first();

        $response = $this->actingAs($user)->get('/bagages');
        $response->assertStatus(200);
        $response->assertViewIs('bagages.index');
    }

    public function test_authenticated_agent_can_create_bagage(): void
    {
        $user = User::where('login', 'admin')->first();

        $payload = [
            'date_depart'   => now()->toDateString(),
            'ville_depart'  => 'Bamako',
            'ville_arrivee' => 'Kayes',
            'programme'     => 'BAMAKO-KAYES 08H',
            'prenom'        => 'Moussa',
            'nom'           => 'Traore',
            'telephone'     => '+223 70 00 00 00',
            'nombre_colis'  => 2,
            'nature'        => 'Valise',
            'poids'         => 15.5,
            'montant'       => 5000,
            'remise'        => 500,
        ];

        $response = $this->actingAs($user)->postJson('/bagages', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('bagages', [
            'montant' => 5000,
            'remise'  => 500,
            'nature'  => 'Valise',
        ]);
    }

    public function test_client_search_autocompletion(): void
    {
        $user = User::where('login', 'admin')->first();

        // Empty search returns empty array
        $resEmpty = $this->actingAs($user)->getJson('/clients/search?q=');
        $resEmpty->assertStatus(200);
        $resEmpty->assertJson([]);

        // Search for known client returns matches
        $resMatch = $this->actingAs($user)->getJson('/clients/search?q=Mamadou');
        $resMatch->assertStatus(200);
        $this->assertNotEmpty($resMatch->json());
    }
}
