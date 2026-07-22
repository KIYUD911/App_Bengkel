<?php

namespace Database\Factories;

use App\Models\SparePart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SparePart>
 */
class SparePartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'part_code'          => 'SP-' . strtoupper($this->faker->unique()->lexify('?????')),
            'name'               => $this->faker->words(3, true),
            'category'           => $this->faker->randomElement(['Oli', 'Busi', 'Kampas Rem']),
            'purchase_price'     => $this->faker->numberBetween(10000, 50000),
            'selling_price'      => $this->faker->numberBetween(60000, 150000),
            'quantity_available' => $this->faker->numberBetween(5, 50),
            'unit'               => 'pcs',
            'warranty_days'      => 0,
        ];
    }
}
