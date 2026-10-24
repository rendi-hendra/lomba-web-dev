<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. ROLE
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_role');
            $table->string('name', 50)->unique();
            $table->timestamps();
        });

        // 2. USERS
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->foreignId('id_role')->constrained('roles', 'id_role')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name')->nullable();
            $table->string('username', 100)->unique();
            $table->string('password');
            $table->timestamps();
        });

        // SESSIONS
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
