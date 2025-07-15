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
        Schema::table('payments', function (Blueprint $table) {
            // Ubah enum untuk menambahkan status 'Refunded'
            $table->enum('payment_status', ['Unpaid', 'Paid', 'Failed', 'Refunded'])
                  ->default('Unpaid')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Kembalikan ke enum semula
            $table->enum('payment_status', ['Unpaid', 'Paid', 'Failed'])
                  ->default('Unpaid')
                  ->change();
        });
    }
};