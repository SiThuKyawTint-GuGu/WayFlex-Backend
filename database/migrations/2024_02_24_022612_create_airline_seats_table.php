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
        Schema::create('airline_seats', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('airline_number_id');
            $table->string("seat_number")->nullable(false);
            $table->enum('seat_status', config('enums.ticket_status_type'));
            $table->unsignedInteger('flight_ticket_price_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airline_seats');
    }
};
