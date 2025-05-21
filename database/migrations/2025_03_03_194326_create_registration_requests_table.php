<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_requests', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('organisation_name', 128);
            $table->string('organisation_main_contact_email', 128);
            $table->string('organisation_main_contact_name', 128);
            $table->string('organisation_coc_number', 8);
            $table->jsonb('client_redirect_uris');
            $table->string('client_fqdn', 256);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_requests');
    }
};
