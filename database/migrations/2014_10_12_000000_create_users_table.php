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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default(null)->nullable(true);;
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->default(null)->nullable(true);
            $table->enum('gender', config('enums.gender_type'));
            $table->string('address')->default(null)->nullable(true);;
            $table->date('date_of_birth')->default(null)->nullable(true);;
            $table->string('nrc')->default(null)->nullable(true);;
            $table->string('image')->default(null)->nullable(true);;
            $table->unsignedInteger('currency_id')->default(null)->nullable(true);;
            $table->unsignedInteger('level_id')->default(null)->nullable(true);;
            $table->unsignedInteger('country_id')->default(null)->nullable(true);;
            $table->unsignedInteger('role_id')->default(null)->nullable(true);
            $table->bigInteger('count')->default(0)->nullable(true);
            $table->bigInteger('coupon_count')->default(0)->nullable(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
