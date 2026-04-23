<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Abonne>
 */
class AbonneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $villes = ['Yaounde', 'Douala', 'Bafoussam', 'Garoua'];
        $types = ['Domestique', 'Professionnel'];
        
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'ville' => fake()->randomElement($villes),
            'quartier' => fake()->streetName(),
            'numeroCompteur' => 'COMP' . fake()->unique()->numberBetween(10000, 99999),
            'typeAbonnement' => fake()->randomElement($types),
        ];
    }
}
