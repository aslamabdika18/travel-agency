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
        Schema::table('bookings', function (Blueprint $table) {
            // Ubah enum untuk menambahkan status 'refunded'
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'refunded'])
                  ->default('pending')
                  ->change();
                  
            // Ubah enum payment_status untuk menambahkan 'refunded'
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'failed', 'cancelled', 'refunded'])
                  ->default('unpaid')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Kembalikan ke enum semula
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])
                  ->default('pending')
                  ->change();
                  
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'failed', 'cancelled'])
                  ->default('unpaid')
                  ->change();
        });
    }
};