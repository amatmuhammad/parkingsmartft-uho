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
         Schema::create('parkings', function (Blueprint $table) {
            $table->id();

            // dari reservation
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('slot_id')->constrained('parking_slots')->onDelete('cascade');

            // waktu parkir
            $table->timestamp('start_time')->nullable();  // waktu scan masuk (QR)
            $table->timestamp('end_time')->nullable();    // waktu scan keluar

            // biaya
            $table->integer('total_fee')->default(0);

            // status parkir
            $table->enum('status', ['ongoing', 'completed'])->default('ongoing');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parkings');
    }
};
