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
        Schema::create('flight_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('flight_ticket_id');
            $table->bigInteger('ticket_price')->nullable(false);
            $table->bigInteger('fare_tax')->nullable(false);
            $table->bigInteger('total_amount')->nullable(false);
            $table->date('transaction_date')->nullable(false);
            $table->unsignedInteger('coupon_id');
            $table->unsignedInteger('payment_form_id');
            $table->integer('passenger_count')->nullable(false);
            $table->integer('seat_count')->nullable(false);
            $table->unsignedInteger('level_discount_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_transactions');
    }
};
