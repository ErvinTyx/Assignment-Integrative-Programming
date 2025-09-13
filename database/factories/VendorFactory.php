<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'status' => null,
            'store_name' => 'Vendor Store',
            'store_address' => fake()->address(),
            'cover_image' => null,
            'created_at' => now()->subDays(30),
            'updated_at'=> now()->subDays(30),
        ];
    }
}
