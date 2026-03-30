<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master checklist templates per service type
        Schema::create('checklist_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('service_type', ['Regular', 'Full']);
            $table->string('item_name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Per-service checklist results
        Schema::create('service_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_record_id')->constrained()->cascadeOnDelete();
            $table->string('item_name');
            $table->enum('status', ['Pending', 'Done', 'N/A'])->default('Pending');
            $table->text('remarks')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_checklist_items');
        Schema::dropIfExists('checklist_templates');
    }
};
