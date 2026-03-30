<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->foreignId('vehicle_make_id')->constrained();
            $table->foreignId('vehicle_model_id')->constrained();
            $table->enum('category', ['Car', 'Van', 'Mini Truck', 'Truck', 'Trailer']);
            $table->string('chassis_number')->nullable();
            $table->unsignedInteger('current_mileage')->default(0); // in KMs
            $table->foreignId('customer_id')->constrained(); // primary driver/owner
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
