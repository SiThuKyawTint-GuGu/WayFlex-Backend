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
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('payment_type_id');
            $table->string('name')->nullable(true)->default(null);
            $table->string('phone_number')->nullable(true)->default(null);
            $table->integer('otp_code')->nullable(true)->default(null);
            $table->string('card_number', 30)->nullable();
            $table->date('expiry_date')->nullable(true)->default(null);
            $table->string('cvv')->nullable(true)->default(null);
            $table->string('status')->nullable(true)->default('active');
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
