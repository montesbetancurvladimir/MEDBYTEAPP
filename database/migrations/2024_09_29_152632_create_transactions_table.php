<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code');
            $table->string('description');
            $table->decimal('value', 10, 2);
            $table->string('currency');
            $table->string('payment_method');
            $table->string('transaction_id')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('payer_id');
            $table->string('buyer_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
