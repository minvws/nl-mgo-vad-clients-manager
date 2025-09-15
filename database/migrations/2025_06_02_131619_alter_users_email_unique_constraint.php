<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', static function (Blueprint $table): void {
            DB::statement('CREATE UNIQUE INDEX unique_active_email ON users(email) WHERE deleted_at IS NULL;');
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_email_deleted_at_unique;');
        });
    }

    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table): void {
            DB::statement('DROP INDEX unique_active_email;');
            DB::statement('CREATE UNIQUE INDEX users_email_deleted_at_unique ON users(email, deleted_at);');
        });
    }
};
