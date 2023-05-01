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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_plan_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('amount')->nullable();
            $table->string('currency')->nullable();
            $table->integer('interval')->nullable();
            $table->string('interval_unit')->nullable();
            $table->integer('trial_period_days')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
