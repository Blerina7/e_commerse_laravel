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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('address_id')->nullable()->constrained('address')->nullOnDelete();
            $table->string('order_number')->unique(); 
            $table->enum('status', [
                'pending',      // sapo u krijua
                'confirmed',    // u konfirmua
                'processing',   // po pergatitet
                'shipped',      // eshte derguar
                'delivered',    // eshte marre
                'cancelled',    // anuluar
                'refunded',     // kthyer
            ])->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('coupon_code')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
