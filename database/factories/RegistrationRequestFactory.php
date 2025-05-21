<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RegistrationRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use function json_encode;

/**
 * @extends Factory<RegistrationRequest>
 */
class RegistrationRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'organisation_name' => $this->faker->company,
            'organisation_main_contact_email' => $this->faker->safeEmail,
            'organisation_main_contact_name' => $this->faker->name,
            'organisation_coc_number' => $this->faker->regexify('[0-9]{8}'),
            'client_redirect_uris' => json_encode([$this->faker->url]),
            'client_fqdn' => $this->faker->domainName,
            'notes' => $this->faker->optional()->text(512),
        ];
    }
}
