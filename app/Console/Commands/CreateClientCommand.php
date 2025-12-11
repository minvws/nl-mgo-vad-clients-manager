<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\TokenEndpointAuthMethod;
use App\Models\Client;
use App\Validations\ClientValidations;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Webmozart\Assert\Assert;

use function explode;
use function sprintf;

class CreateClientCommand extends Command
{
    protected $signature = 'client:create
        {organisation_id : The organisation\'s ID this client belongs to}
        {redirect_uris :   One or more URI\'s to redirect to after authentication (comma-separated)}
        {--client_id= :    Provide a specific UUID to use as primary key for this client}
        {--inactive :      Mark this client inactive (default is active)}';

    protected $description = 'Creates a new client with the given attributes';

    public function handle(): int
    {
        $organisationId = $this->argument('organisation_id');
        Assert::stringNotEmpty($organisationId);

        $redirectUris = $this->argument('redirect_uris');
        Assert::stringNotEmpty($redirectUris);
        $redirectUris = explode(',', $redirectUris);

        $clientId = $this->option('client_id');
        Assert::nullOrString($clientId);
        if ($clientId !== null && !Uuid::isValid($clientId)) {
            $this->error('Client ID must be a valid UUID');

            return SymfonyCommand::FAILURE;
        }

        $clientAttributes = [
            'organisation_id' => $organisationId,
            'redirect_uris' => $redirectUris,
            'token_endpoint_auth_method' => TokenEndpointAuthMethod::NONE,
            'active' => $this->option('inactive') !== true,
        ];

        if ($clientId) {
            $clientAttributes['id'] = $clientId;
        }

        try {
            Validator::make($clientAttributes, ClientValidations::rules())
                ->validate();
        } catch (ValidationException $exception) {
            $this->error($exception->getMessage());

            return SymfonyCommand::FAILURE;
        }

        $client = Client::query()->createQuietly($clientAttributes);

        $this->info(sprintf('Client created: %s', $client->id));

        return SymfonyCommand::SUCCESS;
    }
}
