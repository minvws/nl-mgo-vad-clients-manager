<?php

declare(strict_types=1);

use App\Enums\TokenEndpointAuthMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', static function (Blueprint $table): void {
            $table->string('token_endpoint_auth_method')->default(TokenEndpointAuthMethod::NONE)->after('client_secret');
        });
    }

    public function down(): void
    {
        Schema::table('clients', static function (Blueprint $table): void {
            $table->dropColumn('token_endpoint_auth_method');
        });
    }
};
