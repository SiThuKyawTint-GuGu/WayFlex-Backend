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
            $table->unsignedInteger('passenger_type_id');
            $table->string('card_holder_name')->nullable(false);
            $table->bigInteger('card_number')->nullable(false);
            $table->string('expiry_date')->nullable(false);
            $table->string('cvv')->nullable(false);
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
