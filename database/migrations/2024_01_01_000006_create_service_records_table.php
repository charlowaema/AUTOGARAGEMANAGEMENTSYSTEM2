<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();
            $table->string('garage_entry_no')->unique(); // Auto-generated
            $table->foreignId('vehicle_id')->constrained();
            $table->foreignId('customer_id')->constrained(); // driver on this visit
            $table->enum('service_type', ['Regular', 'Full']);
            $table->unsignedInteger('mileage_at_service');
            $table->unsignedInteger('next_service_mileage');
            $table->date('service_date');
            $table->date('next_service_date');
            $table->enum('status', ['Open', 'In Progress', 'Closed'])->default('Open');
            $table->text('notes')->nullable();
            $table->decimal('total_labour_cost', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};
