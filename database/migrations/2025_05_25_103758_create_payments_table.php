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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('payment_reference')->nullable();
            $table->string('transaction_id')->nullable(); // Menambahkan transaction_id sesuai model
            $table->date('payment_date')->nullable();
            $table->unsignedInteger('total_price');
            $table->enum('payment_status', ['Unpaid', 'Paid','Failed'])->default('Unpaid');
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_status')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Menambahkan indeks untuk mempercepat pencarian
            $table->index('payment_reference');
            $table->index('transaction_id');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
