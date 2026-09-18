<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'last_name' => fake()->lastName(),
            'company_name' => fake()->company(),
            'trade_name' => fake()->company(),
            'federal_tax_id' => fake()->currencyCode(),
            'national_id' => fake()->currencyCode(),
            'email' => fake()->unique()->safeEmail(),
            'phone1' => fake()->phoneNumber(),
            'phone2' => fake()->phoneNumber()
        ];
    }
}
