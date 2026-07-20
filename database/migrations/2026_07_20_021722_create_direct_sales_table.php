<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direct_sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number', 10)->unique(); // format DS-XXXX
            $table->foreignId('customer_id')->nullable()->constrained('customers'); // null = walk-in
            $table->string('walk_in_name')->nullable(); // jika tidak terdaftar
            $table->foreignId('user_id')->constrained('users'); // kasir
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->enum('payment_method', ['tunai', 'transfer', 'kartu']);
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_sales');
    }
};
