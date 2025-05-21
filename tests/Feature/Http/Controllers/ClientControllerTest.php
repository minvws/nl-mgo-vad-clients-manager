<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\ClientController;
use App\Models\Client;
use App\Models\Organisation;
use Carbon\Carbon;
use Tests\TestCase;

use function assert;
use function fake;
use function http_build_query;
use function json_encode;
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
        $clients = Client::factory()->count(ClientController::CLIENT_PAGINATION_SIZE)->create();
        $this->login();
        $response = $this->get('/clients');
        $response->assertStatus(200);

        foreach ($clients as $client) {
            $response->assertSee($client->id);
            $response->assertSee($client->fqdn);
            $response->assertSee($client->redirect_uris);
            $response->assertSee($client->active);
            $response->assertSee($client->organisation->name);
            $response->assertSee($client->organisation->main_contact_email);
        }
    }

    public function testIndexShowsPagination(): void
    {
        Client::factory()
            ->count(fake()->numberBetween(
                ClientController::CLIENT_PAGINATION_SIZE + 1,
                ClientController::CLIENT_PAGINATION_SIZE + 50,
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
            'fqdn' => 'host.test.com',
            'redirect_uris' => [
                'https://host.test.com/callback',
            ],
            'active' => true,
            'organisation_id' => $organisation->id,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/clients');

        $this->assertDatabaseHas('clients', [
            'fqdn' => 'host.test.com',
            'organisation_id' => $organisation->id,
            'redirect_uris' => '["https://host.test.com/callback"]',
        ]);
    }

    public function testStoreWithDifferingFqdn(): void
    {
        $this->login();

        $organisation = Organisation::factory()->create();

        $response = $this->post(route('clients.create'), [
            'fqdn' => 'https://host.test.com',
            'redirect_uris' => 'https://host.anotherhost.com/callback',
            'organisation_id' => $organisation->id,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('fqdn');
        $this->assertDatabaseMissing('clients', [
            'fqdn' => 'https://host.test.com',
            'organisation_id' => $organisation->id,
            'redirect_uris' => json_encode('https://host.anotherhost.com/callback'),
        ]);
    }

    public function testStoreWithDuplicateFqdn(): void
    {
        Client::factory()->create(['fqdn' => 'host.test.com']);

        $this->login();

        $organisation = Organisation::factory()->create();

        $response = $this->post(route('clients.create'), [
            'fqdn' => 'host.test.com',
            'redirect_uris' => ['https://host.test.com/callback'],
            'organisation_id' => $organisation->id,
            'active' => true,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'fqdn' => 'FQDN is al in gebruik.',
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
            'fqdn' => 'host.test.com',
            'redirect_uris' => [
                'https://host.test.com/callback',
            ],
            'active' => true,
            'organisation_id' => $organisation->id,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/clients');

        $this->assertDatabaseHas('clients', [
            'fqdn' => 'host.test.com',
            'organisation_id' => $organisation->id,
            'redirect_uris' => '["https://host.test.com/callback"]',
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
            'fqdn' => 'host.test.com',
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
            'fqdn' => 'host.test.com',
            'active' => true,
            'organisation_id' => $organisation->id,
        ]);

        $response->assertStatus(302);
        assert($response->body(), "redirect_uris.0 is verplicht");
    }

    public function testIndexSearch(): void
    {
        $this->login();
        $client = Client::factory()->create([
            'fqdn' => 'unique-search-term.test.com',
        ]);
        $unexpectedClient = Client::factory()->create([
            'fqdn' => 'unrelated-domain.test.com',
        ]);

        $response = $this->get('/clients?search=unique-search-term');
        $response->assertStatus(200);
        $response->assertSee($client->fqdn);
        $response->assertDontSee($unexpectedClient->fqdn);
    }

    public function testIndexSearchFieldIsVisible(): void
    {
        $this->login();

        $response = $this->get('/clients');
        $response->assertStatus(200);
        $response->assertSee('name="search"', false);
    }

    public function testSearchByFQDN(): void
    {
        $this->login();

        $expectedClient = Client::factory()
            ->for(Organisation::factory())
            ->create([
                'fqdn' => 'unique-search-term.test.com',
            ]);

        $unexpectedClient = Client::factory()
            ->for(Organisation::factory())
            ->create([
                'fqdn' => 'unrelated-domain.test.com',
            ]);

        $response = $this->get('/clients?search=unique-search-term');
        $response->assertStatus(200);

        $response->assertSee($expectedClient->id);
        $response->assertDontSee($unexpectedClient->id);
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

    public function testCaseInsensitiveSearch(): void
    {
        $this->login();

        $expectedClient = Client::factory()->for(Organisation::factory())->create(['fqdn' => 'https://UPPERCASE.test.com']);

        $response = $this->get('/clients?search=uppercase');
        $response->assertStatus(200);

        $response->assertSee($expectedClient->id);
    }

    public function testEmptySearchShowsAllClients(): void
    {
        $this->login();

        $clients = Client::factory()->count(5)->create();

        $response = $this->get('/clients?search=');
        $response->assertStatus(200);

        foreach ($clients as $client) {
            $response->assertSee($client->id);
            $response->assertSee($client->fqdn);
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

    public function testIndexFallbackToDefaultSort(): void
    {
        $this->login();
        $clientA = Client::factory()->create([
            'created_at' => Carbon::yesterday(),
        ]);
        $clientB = Client::factory()->create([
            'created_at' => Carbon::now(),
        ]);

        $queryParams = [
            'sort' => ['blaat'],
            'direction' => ['asc'],
        ];

        $response = $this->get('/clients?' . http_build_query($queryParams));
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $clientB->id,
            $clientA->id,
        ]);
    }

    public function testIndexFqdnSort(): void
    {
        $this->login();
        $clientA = Client::factory()->create([
            'fqdn' => 'https://aaa.test.com',
        ]);
        $clientB = Client::factory()->create([
            'fqdn' => 'https://bbb.test.com',
        ]);

        $response = $this->get('/clients?sort=fqdn&direction=asc');
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $clientA->id,
            $clientB->id,
        ]);

        $response = $this->get('/clients?sort=fqdn&direction=desc');
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $clientB->id,
            $clientA->id,
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
}
