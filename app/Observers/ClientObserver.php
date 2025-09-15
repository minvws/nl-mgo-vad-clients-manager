<?php

declare(strict_types=1);

namespace App\Observers;

use App\Services\ClientChangeNotifier;

class ClientObserver
{
    public function __construct(
        private readonly ClientChangeNotifier $clientChangeNotifier,
    ) {
    }

    public function created(): void
    {
        $this->clientChangeNotifier->notify();
    }

    public function updated(): void
    {
        $this->clientChangeNotifier->notify();
    }

    public function deleted(): void
    {
        $this->clientChangeNotifier->notify();
    }
}
