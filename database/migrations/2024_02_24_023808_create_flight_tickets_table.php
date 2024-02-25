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
        Schema::create('flight_tickets', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable(false);
            $table->unsignedInteger('flight_category_id');
            $table->unsignedInteger('system_id');
            $table->string("description")->nullable(true)->default(null);
            $table->unsignedInteger('departure_airport_id');
            $table->time("departure_time")->nullable(false);
            $table->date("departure_date")->nullable(false);
            $table->unsignedInteger('departure_city_id');
            $table->time("arrival_time")->nullable(false);
            $table->date("arrival_date")->nullable(false);
            $table->unsignedInteger('arrival_city_id');
            $table->unsignedInteger('rating_id');
            $table->date("return_date")->nullable(true)->default(null);
            $table->unsignedInteger('arrive_airport_id');
            $table->time("duration")->nullable(false);
            $table->unsignedInteger('trip_status_id');
            $table->unsignedInteger('flight_trip_id');
            $table->unsignedInteger('weight_id');
            $table->unsignedInteger('ticket_status_id');
            $table->unsignedInteger('meal_id');
            $table->string('image')->nullable(true)->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_tickets');
    }
};
