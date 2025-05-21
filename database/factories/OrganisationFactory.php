<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organisation>
 */
class OrganisationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'main_contact_email' => $this->faker->safeEmail,
            'main_contact_name' => $this->faker->name,
            'name' => $this->faker->company,
            'coc_number' => $this->faker->regexify('[0-9]{8}'),
            'notes' => $this->faker->optional()->text(512),
        ];
    }
}
