<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facture>
 */
class FactureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $consommation = $this->faker->numberBetween(50, 500);
        $tarifParM3 = 100;
        
        return [
            'abonne_id' => \App\Models\Abonne::factory(),
            'consommation' => $consommation,
            'montantTotal' => $consommation * $tarifParM3,
            'dateEmission' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'statut' => $this->faker->randomElement(['Emise', 'Payee']),
        ];
    }
}
