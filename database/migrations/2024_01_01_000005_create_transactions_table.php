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
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('gateway_id')->nullable()->constrained('gateways')->onDelete('set null');
            $table->string('external_id')->nullable(); // ID from gateway
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->integer('amount'); // Total amount in cents
            $table->string('card_last_numbers', 4);
            $table->json('gateway_response')->nullable(); // Store full gateway response
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('client_id');
            $table->index('status');
            $table->index('external_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

