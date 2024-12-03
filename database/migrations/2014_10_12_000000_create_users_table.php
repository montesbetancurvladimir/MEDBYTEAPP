<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void{
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('first_name');
            $table->foreignId('country_id')->references('id')->on('paises');
            $table->string('phone');
            $table->string('password');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('confirmacion_cuenta')->default(false);
            $table->boolean('enable')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('users');
    }
};
