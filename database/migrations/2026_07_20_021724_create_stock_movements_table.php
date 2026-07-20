<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spare_part_id')->constrained('spare_parts');
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders');
            $table->foreignId('direct_sale_id')->nullable()->constrained('direct_sales');
            $table->enum('type', ['IN', 'OUT']);
            $table->integer('quantity');
            $table->string('reason'); // restock, service_usage, direct_sale, wo_cancel_rollback, adjustment
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
