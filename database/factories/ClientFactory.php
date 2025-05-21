<?php

declare(strict_types=1);

namespace Database\Factories;

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
            'fqdn' => $urls['fqdn'],
            'active' => $this->faker->boolean,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getUrls(): array
    {
        $fqdn = $this->faker->unique()->domainName();
        $redirect_uris = [];
        for ($i = 0; $i < 3; $i++) {
            $redirect_uris[] = 'https://' . $fqdn . '/' . $this->faker->word();
        }

        return [
            'fqdn' => $fqdn,
            'redirect_uris' => $redirect_uris,
        ];
    }
}
