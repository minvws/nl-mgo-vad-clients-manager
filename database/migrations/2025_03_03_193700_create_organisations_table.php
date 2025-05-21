<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisations', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('main_contact_email', 128);
            $table->string('main_contact_name', 128);
            $table->string('name', 128);
            $table->string('coc_number', 8);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisations');
    }
};
