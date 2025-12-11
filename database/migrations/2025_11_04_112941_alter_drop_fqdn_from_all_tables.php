<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', static function (Blueprint $table): void {
            $table->dropColumn('fqdn');
        });

        Schema::table('registration_requests', static function (Blueprint $table): void {
            $table->dropColumn('client_fqdn');
        });
    }

    public function down(): void
    {
        Schema::table('clients', static function (Blueprint $table): void {
            $table->string('fqdn', 256)->unique()->after('organisation_id');
        });

        Schema::table('registration_requests', static function (Blueprint $table): void {
            $table->string('client_fqdn', 256)->after('organisation_coc_number');
        });
    }
};
