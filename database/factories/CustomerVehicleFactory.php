<?php

namespace Database\Factories;

use App\Models\CustomerVehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerVehicle>
 */
class CustomerVehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id'   => \App\Models\Customer::factory(),
            'license_plate' => strtoupper($this->faker->bothify('B #### ???')),
            'vehicle_type'  => $this->faker->randomElement(['Mobil', 'Motor']),
            'brand'         => $this->faker->randomElement(['Honda', 'Yamaha', 'Suzuki']),
            'model'         => $this->faker->word(),
            'year'          => $this->faker->numberBetween(2010, 2024),
        ];
    }
}
