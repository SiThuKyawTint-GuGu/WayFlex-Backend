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
        Schema::create('level_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('level_id');
            $table->integer("amount")->nullable(true)->default(0);
            $table->integer("discount_percentage")->nullable(true)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_discounts');
    }
};
