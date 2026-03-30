<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('part_number')->nullable();
            $table->string('category')->nullable(); // Oil, Filter, Belt, etc.
            $table->unsignedInteger('quantity_in_stock')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->string('unit')->default('pcs'); // pcs, litres, metres
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
