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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('travel_package_id')->constrained('travel_packages')->cascadeOnDelete();
            $table->string('booking_reference')->unique();
            $table->date('booking_date');
            $table->unsignedInteger('person_count');
            $table->unsignedInteger('base_price'); // Harga dasar per orang
            $table->unsignedInteger('additional_price')->default(0); // Biaya tambahan (asuransi, upgrade, dll)
            $table->unsignedInteger('tax_amount')->default(0); // Pajak yang dihitung
            $table->unsignedInteger('total_price'); // Total = (base_price * person_count) + additional_price + tax_amount
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'failed', 'cancelled'])->default('unpaid');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
