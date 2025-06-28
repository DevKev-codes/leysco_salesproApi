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
    $table->string('order_number')->unique();
    $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->decimal('subtotal', 12, 2);
    $table->decimal('tax_amount', 12, 2);
    $table->decimal('discount_amount', 12, 2)->default(0.00);
    $table->decimal('total_amount', 12, 2);
    $table->enum('status', [
        'draft', 'confirmed', 'processing', 'shipped',
        'delivered', 'cancelled', 'refunded'
    ])->default('draft');
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index('order_number');
    $table->index('customer_id');
    $table->index('status');
    $table->index('created_at');
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
