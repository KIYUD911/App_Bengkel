<?php

namespace Database\Factories;

use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wo_number'           => 'WO-' . $this->faker->unique()->numberBetween(1000, 9999),
            'customer_id'         => \App\Models\Customer::factory(),
            'customer_vehicle_id' => \App\Models\CustomerVehicle::factory(),
            'user_id'             => \App\Models\User::factory(),
            'status'              => 'pending',
            'complaint'           => $this->faker->sentence(),
            'total_parts_cost'    => 0,
            'labour_cost'         => 0,
            'grand_total'         => 0,
        ];
    }
}
