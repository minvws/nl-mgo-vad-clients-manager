<?php

declare(strict_types=1);

namespace Tests\Feature\Observer;

use App\Enums\TokenEndpointAuthMethod;
use App\Models\Client;
use App\Observers\ClientObserver;
use App\Services\ClientChangeNotifier;
use App\Services\ClientSecretProvisioner;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(ClientObserver::class)]
class ClientObserverTest extends TestCase
{
    private ClientChangeNotifier|MockObject $mockNotifier;
    private ClientSecretProvisioner|MockObject $mockProvisioner;
    private ClientObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockNotifier = $this->getMockBuilder(ClientChangeNotifier::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['notify'])
            ->getMock();

        $this->mockProvisioner = $this->getMockBuilder(ClientSecretProvisioner::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generateAndNotify'])
            ->getMock();

        $this->observer = new ClientObserver($this->mockNotifier, $this->mockProvisioner);
    }

    public function testCreatedGeneratesSecretAndNotifies(): void
    {
        $client = Client::factory()->make([
            'token_endpoint_auth_method' => TokenEndpointAuthMethod::CLIENT_SECRET,
        ]);

        $this->mockProvisioner->expects($this->once())
            ->method('generateAndNotify')
            ->with($client);

        $this->mockNotifier->expects($this->once())
            ->method('notify');

        $this->observer->created($client);
    }

    public function testCreatedDoesNotGenerateSecretForNoneAuthMethod(): void
    {
        $client = Client::factory()->make([
            'token_endpoint_auth_method' => TokenEndpointAuthMethod::NONE,
        ]);

        $this->mockProvisioner->expects($this->never())
            ->method('generateAndNotify');

        $this->mockNotifier->expects($this->once())
            ->method('notify');

        $this->observer->created($client);
    }

    public function testUpdatedNotifiesWithoutSecretGeneration(): void
    {
        $client = Client::factory()->make();

        $this->mockNotifier->expects($this->once())
            ->method('notify');

        $this->mockProvisioner->expects($this->never())
            ->method('generateAndNotify');

        $this->observer->updated($client);
    }

    public function testDeletedNotifiesWithoutSecretGeneration(): void
    {
        $client = Client::factory()->make();

        $this->mockNotifier->expects($this->once())
            ->method('notify');

        $this->mockProvisioner->expects($this->never())
            ->method('generateAndNotify');

        $this->observer->deleted($client);
    }
}
