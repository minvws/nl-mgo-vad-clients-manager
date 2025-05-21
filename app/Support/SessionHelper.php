<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Database\DatabaseManager;

readonly class SessionHelper
{
    public function __construct(private DatabaseManager $databaseManager)
    {
    }

    public function invalidateUser(string $userId): void
    {
        $this->databaseManager
            ->table('sessions')
            ->where(['user_id' => $userId])
            ->delete();
    }
}
