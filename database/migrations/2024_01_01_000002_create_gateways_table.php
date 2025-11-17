<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->string('type')->default('gateway1'); // gateway1 or gateway2
            $table->json('credentials')->nullable(); // For storing auth credentials
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gateways');
    }
};

