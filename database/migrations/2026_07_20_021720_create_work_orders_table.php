<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number', 10)->unique(); // format WO-XXXX
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('customer_vehicle_id')->constrained('customer_vehicles');
            $table->foreignId('user_id')->constrained('users'); // kasir pembuat
            $table->text('complaint'); // keluhan pelanggan
            $table->text('mechanic_notes')->nullable(); // solusi mekanik
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('labour_cost', 12, 2)->default(0);
            $table->decimal('total_parts_cost', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->enum('payment_method', ['tunai', 'transfer', 'kartu'])->nullable();
            $table->timestamp('paid_at')->nullable();
            // Field cancel
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
