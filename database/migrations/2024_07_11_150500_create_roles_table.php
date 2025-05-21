<?php

declare(strict_types=1);

use App\Enums\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', static function (Blueprint $table): void {
            $table->string('name')->primary();
        });

        Schema::create('role_user', static function (Blueprint $table): void {
            $table->foreignUuid('user_id')->constrained();
            $table->string('role_name');
            $table->foreign('role_name')->references('name')->on('roles');
        });

        DB::transaction(static function (): void {
            foreach (Role::cases() as $role) {
                DB::table('roles')->insert(['name' => $role->value]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};
