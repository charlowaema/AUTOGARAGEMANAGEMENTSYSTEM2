<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            // Payment details
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['Cash', 'M-Pesa'])->default('Cash');
            $table->enum('status', ['Pending', 'Paid', 'Failed'])->default('Pending');

            // M-Pesa specific fields
            $table->string('phone_number')->nullable();           // e.g. 2547XXXXXXXX
            $table->string('mpesa_reference')->nullable();        // Safaricom confirmation code e.g. QKH7HJ83JK
            $table->string('checkout_request_id')->nullable();    // STK Push CheckoutRequestID
            $table->string('merchant_request_id')->nullable();    // STK Push MerchantRequestID
            $table->text('mpesa_response')->nullable();           // Raw JSON response from Daraja

            // Receipt
            $table->string('receipt_number')->unique()->nullable(); // Auto-generated
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
