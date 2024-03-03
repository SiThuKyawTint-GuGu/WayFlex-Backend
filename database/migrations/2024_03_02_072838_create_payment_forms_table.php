<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('payment_type_id');
            $table->string('card_holder_name')->nullable(true)->default(null);
            $table->string('card_number', 30)->nullable();
            $table->string('expiry_date')->nullable(true)->default(null);
            $table->string('cvv')->nullable(true)->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_forms');
    }
};
