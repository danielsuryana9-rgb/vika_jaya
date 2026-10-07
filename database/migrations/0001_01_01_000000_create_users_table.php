<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabang', function (Blueprint $t) {
            $t->id('id_cabang');
            $t->string('nama_cabang', 100);
            $t->string('alamat')->nullable();
            $t->string('telepon', 20)->nullable();
            $t->timestamps();
        });

        Schema::create('users', function (Blueprint $t) {
            $t->id('id_user');
            $t->foreignId('id_cabang')->nullable()->constrained('cabang', 'id_cabang')->cascadeOnUpdate()->restrictOnDelete();
            $t->string('nama', 100);
            $t->string('username', 50)->unique();
            $t->string('password');
            $t->string('no_hp', 20)->nullable();
            $t->enum('role', ['owner', 'kasir']);
            $t->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $t->rememberToken();
            $t->timestamps();
        });

        Schema::create('sessions', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->foreignId('user_id')->nullable()->index();
            $t->string('ip_address', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->longText('payload');
            $t->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('cabang');
    }
};
