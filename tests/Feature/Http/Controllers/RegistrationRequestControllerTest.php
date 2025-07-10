<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function config;
use function explode;
use function json_encode;
use function route;

class RegistrationRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testRegistrationRequestFormCanBeRendered(): void
    {
        $response = $this->get(route('registration-requests.create'));

        $response->assertStatus(200);
    }

    public function testRegistrationRequestCanBeSubmitted(): void
    {
        $response = $this->post(route('registration-requests.store'), [
            'organisation_name' => 'Test Organisation',
            'organisation_main_contact_name' => 'John Doe',
            'organisation_main_contact_email' => 'john.doe@example.com',
            'organisation_coc_number' => '00345678',
            'client_fqdn' => 'example.com',
            'client_redirect_uris' => ['https://example.com/callback'],
        ]);

        $response->assertRedirect(route('registration-requests.thank-you'));

        $this->assertDatabaseHas('registration_requests', [
            'organisation_name' => 'Test Organisation',
            'organisation_main_contact_name' => 'John Doe',
            'organisation_main_contact_email' => 'john.doe@example.com',
            'organisation_coc_number' => '00345678',
            'client_fqdn' => 'example.com',
            'client_redirect_uris' => json_encode(['https://example.com/callback']),
        ]);
    }

    public function testRegistrationRequestValidation(): void
    {
        $response = $this->post(route('registration-requests.store'), [
            'organisation_name' => '',
            'organisation_main_contact_name' => '',
            'organisation_main_contact_email' => 'invalid-email',
            'organisation_coc_number' => '123',
            'client_fqdn' => 'invalid-fqdn',
            'client_redirect_uris' => [],
        ]);

        $response->assertSessionHasErrors([
            'organisation_name',
            'organisation_main_contact_name',
            'organisation_main_contact_email',
            'organisation_coc_number',
            'client_fqdn',
            'client_redirect_uris',
        ]);
    }

    public function testRegistrationRequestValidationFailsOnRedirectUris(): void
    {
        $response = $this->post(route('registration-requests.store'), [
            'organisation_name' => 'Test Organisation',
            'organisation_main_contact_name' => 'John Doe',
            'organisation_main_contact_email' => 'john.doe@example.com',
            'organisation_coc_number' => '12345678',
            'client_fqdn' => 'example.com',
            'client_redirect_uris' => ['http://example.com/redirect', 'http://wrong'],
        ]);

        $response->assertSessionHasErrors([
            'client_redirect_uris.*',
        ]);

        $this->assertEquals(
            ['client_redirect_uris.1' => ['De host van de URI http://wrong komt niet overeen met de host van het FQDN example.com.']],
            $response->exception->validator->errors()->toArray(),
        );
    }

    public function testRegistrationRequestThrottling(): void
    {
        $maxAttempts = (int) explode(',', config('throttle.registration_requests'))[0];

        // Submit requests up to the throttle limit
        for ($i = 0; $i < $maxAttempts; $i++) {
            $this->post(route('registration-requests.store'), [
                'organisation_name' => 'Test Organisation ' . $i,
                'organisation_main_contact_name' => 'John Doe',
                'organisation_main_contact_email' => 'john.doe@example.com',
                'organisation_coc_number' => '12345678',
                'client_fqdn' => 'example' . $i . '.com',
                'client_redirect_uris' => ['https://example.com/callback'],
            ]);
        }

        // Try to submit one more request
        $response = $this->post(route('registration-requests.store'), [
            'organisation_name' => 'Test Organisation ' . ($maxAttempts + 1),
            'organisation_main_contact_name' => 'John Doe',
            'organisation_main_contact_email' => 'john.doe@example.com',
            'organisation_coc_number' => '12345678',
            'client_fqdn' => 'example' . ($maxAttempts + 1) . '.com',
            'client_redirect_uris' => ['https://example.com/callback'],
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    public function testThankYouPageCanBeRendered(): void
    {
        $response = $this->get(route('registration-requests.thank-you'));

        $response->assertStatus(200);
    }
}
