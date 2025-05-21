<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Organisation;
use Tests\TestCase;

use function route;
use function sprintf;

class OrganisationControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $this->login();
        $response = $this->get('/organisations');
        $response->assertStatus(200);
    }

    public function testIndexShowsOrganisations(): void
    {
        $organisations = Organisation::factory()->count(5)->create();
        $this->login();

        $response = $this->get('/organisations');
        $response->assertStatus(200);

        foreach ($organisations as $organisation) {
            $response->assertSee($organisation->name);
            $response->assertSee($organisation->main_contact_name);
            $response->assertSee($organisation->main_contact_email);
            $response->assertSee($organisation->coc_number);
        }
    }

    public function testCreate(): void
    {
        $this->login();
        $response = $this->get('/organisations/create');
        $response->assertStatus(200);
    }

    public function testStore(): void
    {
        $this->login();
        $response = $this->post(route('organisations.store'), [
            'name' => 'Test Organisation',
            'main_contact_name' => 'John Doe',
            'main_contact_email' => 'john@example.com',
            'coc_number' => '12345678',
            'notes' => 'Test notes',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/organisations');

        $this->assertDatabaseHas('organisations', [
            'name' => 'Test Organisation',
            'main_contact_name' => 'John Doe',
            'main_contact_email' => 'john@example.com',
            'coc_number' => '12345678',
            'notes' => 'Test notes',
        ]);
    }

    public function testStoreWithInvalidData(): void
    {
        $this->login();
        $response = $this->post(route('organisations.store'), [
            'name' => 'Test Organisation',
            'main_contact_name' => 'John Doe',
            'main_contact_email' => 'invalid-email', // Invalid email format
            'coc_number' => '123', // Too short (should be 8 characters)
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['main_contact_email', 'coc_number']);
    }

    public function testEdit(): void
    {
        $this->login();
        $organisation = Organisation::factory()->create();
        $response = $this->get(sprintf('/organisations/%s', $organisation->id));
        $response->assertStatus(200);
        $response->assertSee($organisation->name);
        $response->assertSee($organisation->main_contact_name);
        $response->assertSee($organisation->main_contact_email);
        $response->assertSee($organisation->coc_number);
    }

    public function testUpdate(): void
    {
        $this->login();
        $organisation = Organisation::factory()->create();
        $response = $this->put(sprintf('/organisations/%s', $organisation->id), [
            'name' => 'Updated Organisation',
            'main_contact_name' => 'Jane Smith',
            'main_contact_email' => 'jane@example.com',
            'coc_number' => '87654321',
            'notes' => 'Updated notes',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/organisations');

        $this->assertDatabaseHas('organisations', [
            'id' => $organisation->id,
            'name' => 'Updated Organisation',
            'main_contact_name' => 'Jane Smith',
            'main_contact_email' => 'jane@example.com',
            'coc_number' => '87654321',
            'notes' => 'Updated notes',
        ]);
    }

    public function testUpdateWithInvalidData(): void
    {
        $this->login();
        $organisation = Organisation::factory()->create();
        $response = $this->put(sprintf('/organisations/%s', $organisation->id), [
            'name' => 'Updated Organisation',
            'main_contact_name' => 'Jane Smith',
            'main_contact_email' => 'invalid-email', // Invalid email format
            'coc_number' => '123', // Too short, min 8
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['main_contact_email', 'coc_number']);
    }
}
