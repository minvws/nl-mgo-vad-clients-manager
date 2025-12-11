<?php

declare(strict_types=1);

namespace App\Services\Health;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use PDOException;

class DatabaseHealthService
{
    public function isHealthy(): bool
    {
        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            return true;
        } catch (PDOException | QueryException $e) {
            return false;
        }
    }
}
