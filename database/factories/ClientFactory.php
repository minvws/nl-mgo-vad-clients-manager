<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TokenEndpointAuthMethod;
use App\Models\Client;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $urls = $this->getUrls();
        return [
            'id' => Str::uuid(),
            'organisation_id' => Organisation::factory(),
            'redirect_uris' => $urls['redirect_uris'],
            'token_endpoint_auth_method' => TokenEndpointAuthMethod::NONE,
            'active' => $this->faker->boolean,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getUrls(): array
    {
        $redirect_uris = [];
        for ($i = 0; $i < 3; $i++) {
            $redirect_uris[] = $this->faker->url();
        }

        return [
            'redirect_uris' => $redirect_uris,
        ];
    }

    public function active(): self
    {
        return $this->state(fn() => [
            'active' => true,
        ]);
    }
}
