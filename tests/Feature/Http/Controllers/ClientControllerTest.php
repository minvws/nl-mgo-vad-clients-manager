<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Enums\TokenEndpointAuthMethod;
use App\Models\Client;
use App\Models\Organisation;
use App\Models\User;
use App\Support\I18n;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

use function assert;
use function config;
use function fake;
use function http_build_query;
use function route;
use function sprintf;
use function substr;

class ClientControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $this->login();

        $response = $this->get('/clients');
        $response->assertStatus(200);
    }

    public function testIndexShowsClients(): void
    {
        $clients = Client::factory()->count(3)->create();
        $this->login();
        $response = $this->get('/clients');
        $response->assertStatus(200);

        foreach ($clients as $client) {
            $response->assertSee($client->id);
            $response->assertSee($client->redirect_uris);
            $response->assertSee($client->active);
            $response->assertSee($client->organisation->name);
            $response->assertSee($client->organisation->main_contact_email);
        }
    }

    public function testIndexShowsPagination(): void
    {
        $extraSmallPageSize = 1;

        config()->set(['app.default_pagination_size' => $extraSmallPageSize]);

        Client::factory()
            ->count(fake()->numberBetween(
                $extraSmallPageSize + 1,
                $extraSmallPageSize + 2,
            ))
            ->create();

        $this->login();

        $response = $this->get('/clients');
        $response->assertStatus(200);

        $response->assertSee("/clients?page=2");
    }

    public function testCreate(): void
    {
        $this->login();

        $response = $this->get('/clients/create');
        $response->assertStatus(200);
    }

    public function testStore(): void
    {
        $this->login();

        $organisation = Organisation::factory()->create();

        $response = $this->post(route('clients.create'), [
            'redirect_uris' => [
                'https://host.test.com/callback',
            ],
            'active' => true,
            'organisation_id' => (string) $organisation->id,
            'token_endpoint_auth_method' => TokenEndpointAuthMethod::CLIENT_SECRET->value,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/clients');

        $this->assertDatabaseHas('clients', [
            'organisation_id' => $organisation->id,
            'redirect_uris' => '["https://host.test.com/callback"]',
            'active' => true,
            'token_endpoint_auth_method' => 'client_secret_post',
        ]);
    }

    public function testEdit(): void
    {
        $this->login();

        $client = Client::factory()->create();

        $response = $this->get(sprintf('/clients/%s', $client->id));
        $response->assertStatus(200);
    }

    public function testUpdate(): void
    {
        $this->login();

        $organisation = Organisation::factory()->create();
        $client = Client::factory()
            ->for($organisation)
            ->create();

        $response = $this->put(sprintf('/clients/%s', $client->id), [
            'redirect_uris' => [
                'https://host.test.com/callback',
            ],
            'active' => true,
            'organisation_id' => (string) $organisation->id,
            'token_endpoint_auth_method' => TokenEndpointAuthMethod::CLIENT_SECRET->value,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/clients');

        $this->assertDatabaseHas('clients', [
            'organisation_id' => $organisation->id,
            'redirect_uris' => '["https://host.test.com/callback"]',
            'active' => true,
            'token_endpoint_auth_method' => 'client_secret_post',
        ]);
    }

    public function testUpdateRedirectUrisIsNotEmpty(): void
    {
        $this->login();

        $organisation = Organisation::factory()->create();
        $client = Client::factory()
            ->for($organisation)
            ->create();

        $response = $this->put(sprintf('/clients/%s', $client->id), [
            'redirect_uris' => [
                '',
                'https://host.test.com/callback',
            ],
            'active' => true,
            'organisation_id' => $organisation->id,
        ]);

        $response->assertStatus(302);
        assert($response->body(), "redirect_uris.0 is verplicht");
    }

    public function testUpdateAtLeast1RedirectUrisIsGiven(): void
    {
        $this->login();

        $organisation = Organisation::factory()->create();
        $client = Client::factory()
            ->for($organisation)
            ->create();

        $response = $this->put(sprintf('/clients/%s', $client->id), [
            'active' => true,
            'organisation_id' => $organisation->id,
        ]);

        $response->assertStatus(302);
        assert($response->body(), "redirect_uris.0 is verplicht");
    }

    public function testIndexSearch(): void
    {
        $this->login();
        $client = Client::factory()->create();
        $unexpectedClient = Client::factory()->create();

        $response = $this->get('/clients?search=' . substr((string) $client->id, 0, 8));
        $response->assertStatus(200);
        $response->assertSee($client->id);
        $response->assertDontSee($unexpectedClient->id);
    }

    public function testIndexSearchFieldIsVisible(): void
    {
        $this->login();

        $response = $this->get('/clients');
        $response->assertStatus(200);
        $response->assertSee('name="search"', false);
    }

    public function testSearchByPartialClientID(): void
    {
        $this->login();
        $expectedClient = Client::factory()->for(Organisation::factory())->create();
        $unexpectedClient = Client::factory()->for(Organisation::factory())->create();
        $searchTerm = substr((string) $expectedClient->id, 0, 8);

        $response = $this->get('/clients?search=' . $searchTerm);
        $response->assertStatus(200);

        $response->assertSee($expectedClient->id);
        $response->assertDontSee($unexpectedClient->id);
    }

    public function testSearchByOrganisationName(): void
    {
        $this->login();

        $expectedClient = Client::factory()
            ->for(
                Organisation::factory()->state([
                    'name' => 'Special Organisation Name',
                ]),
            )->create();

        $unexpectedClient = Client::factory()->for(Organisation::factory())->create();

        $response = $this->get('/clients?search=Special Organisation');
        $response->assertStatus(200);

        $response->assertSee($expectedClient->id);
        $response->assertDontSee($unexpectedClient->id);
    }

    public function testSearchByContactEmail(): void
    {
        $this->login();

        $expectedClient = Client::factory()->for(
            Organisation::factory()->state(['main_contact_email' => 'special-contact@example.com']),
        )->create();
        $unexpectedClient = Client::factory()->for(Organisation::factory())->create();

        $response = $this->get('/clients?search=special-contact');
        $response->assertStatus(200);

        $response->assertSee($expectedClient->id);
        $response->assertDontSee($unexpectedClient->id);
    }

    public function testEmptySearchShowsAllClients(): void
    {
        $this->login();

        $clients = Client::factory()->count(5)->create();

        $response = $this->get('/clients?search=');
        $response->assertStatus(200);

        foreach ($clients as $client) {
            $response->assertSee($client->id);
        }
    }

    public function testIndexDefaultSort(): void
    {
        $this->login();
        $clientA = Client::factory()->create([
            'created_at' => Carbon::yesterday(),
        ]);
        $clientB = Client::factory()->create([
            'created_at' => Carbon::now(),
        ]);

        $response = $this->get('/clients');
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $clientB->id,
            $clientA->id,
        ]);
    }

    public function testIndexRequestDoesNotAcceptArbitrarySortParameter(): void
    {
        $this->login();
        Client::factory()->create([
            'created_at' => Carbon::yesterday(),
        ]);
        Client::factory()->create([
            'created_at' => Carbon::now(),
        ]);

        $queryParams = [
            'sort' => fake()->word(),
            'direction' => 'asc',
        ];

        $response = $this->get('/clients?' . http_build_query($queryParams));
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'sort' => I18n::trans('validation.in', [
                'attribute' => 'sort',
            ]),
        ]);
    }

    public function testIndexOrganisationSort(): void
    {
        $this->login();
        $clientA = Client::factory()
            ->for(Organisation::factory()->create([
                'name' => 'Company A',
            ]))
            ->create();
        $clientB = Client::factory()
            ->for(Organisation::factory()->create([
                'name' => 'Company B',
            ]))
            ->create();

        $response = $this->get('/clients?sort=organisations.name&direction=asc');
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $clientA->id,
            $clientB->id,
        ]);

        $response = $this->get('/clients?sort=organisations.name&direction=desc');
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $clientB->id,
            $clientA->id,
        ]);
    }

    public function testIndexOrganisationSortAndSearch(): void
    {
        $this->login();
        $clientA = Client::factory()
            ->for(Organisation::factory()->create([
                'name' => 'Company AA',
            ]))
            ->create();
        $clientB = Client::factory()
            ->for(Organisation::factory()->create([
                'name' => 'Company AB',
            ]))
            ->create();

        $clientC = Client::factory()
            ->for(Organisation::factory()->create([
                'name' => 'Company C',
            ]))
            ->create();

        $response = $this->get('/clients?sort=organisations.name&direction=asc&search=company a');
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $clientA->id,
            $clientB->id,
        ]);
        $response->assertDontSee($clientC->id);

        $response = $this->get('/clients?sort=organisations.name&direction=desc&search=company a');
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $clientB->id,
            $clientA->id,
        ]);
        $response->assertDontSee($clientC->id);
    }

    #[dataProvider('activeStatusProvider')]
    public function testClientIndexCanBeFilteredOnActiveStatus(mixed $active, int $expectedCount): void
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();

        Client::factory()
            ->count(2)
            ->for($organisation)
            ->create(['active' => true]);

        Client::factory()
            ->count(3)
            ->for($organisation)
            ->create(['active' => false]);

        $response = $this->actingAs($user)
            ->get(route('clients.index', ['active' => $active]));

        $response->assertOk();
        $response->assertViewHas('clients', function ($clients) use ($expectedCount) {
            return $clients->count() === $expectedCount;
        });
    }

    public static function activeStatusProvider(): array
    {
        return [
            'active clients' => [true, 2],
            'inactive clients' => [false, 3],
            'both active and inactive clients with empty string' => ['', 5],
            'both active and inactive clients with null value' => [null, 5],
            'active clients with value 1' => [1, 2],
        ];
    }
}
