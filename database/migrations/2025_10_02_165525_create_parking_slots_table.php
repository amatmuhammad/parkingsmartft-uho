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
        Schema::create('parking_slots', function (Blueprint $table) {
            $table->id();
            $table->string('slot_code')->unique();
            $table->string('slot_name');
            $table->enum('status', ['available', 'booked', 'occupied'])->default('available');
            $table->foreignId('area_id')->constrained('parking_areas')->onDelete('cascade');
            $table->decimal('latitude', 10, 7)->nullable();   // posisi slot
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_slots');
    }
};
