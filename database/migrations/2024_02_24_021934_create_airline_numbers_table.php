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
        Schema::create('airline_numbers', function (Blueprint $table) {
            $table->id();
            $table->string("number")->nullable(false);
            $table->unsignedInteger('airline_id');
            $table->integer("total_seat")->nullable(false);
            $table->unsignedInteger('flight_class_id');
            $table->unsignedInteger('flight_ticket_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airline_numbers');
    }
};
