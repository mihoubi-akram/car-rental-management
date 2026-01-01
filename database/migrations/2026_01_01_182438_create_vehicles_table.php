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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('model', 100);
            $table->integer('year');
            $table->string('registration_number', 20)->unique();
            $table->string('vin', 17)->unique()->nullable();
            $table->string('color', 50)->nullable();
            $table->string('fuel_type', 20);
            $table->string('transmission', 20);
            $table->unsignedTinyInteger('seats');
            $table->unsignedInteger('mileage')->default(0);
            $table->date('last_maintenance_date')->nullable();
            $table->unsignedInteger('next_maintenance_mileage')->nullable();
            $table->decimal('daily_rate', 8, 2);
            $table->decimal('weekly_rate', 8, 2)->nullable();
            $table->string('category', 30);
            $table->string('availability_status', 20)->default('available');
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable();
            $table->timestamps();

            $table->index('availability_status');
            $table->index('is_active');
            $table->index(['brand_id', 'model']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
