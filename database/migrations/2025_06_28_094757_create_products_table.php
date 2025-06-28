<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
 
        Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('sku')->unique();
    $table->string('name');
    $table->text('description');
    $table->decimal('price', 10, 2);
    $table->decimal('cost_price', 10, 2);
    $table->decimal('tax_rate', 5, 2)->default(0); // Added tax_rate column
    $table->string('unit')->nullable();
    $table->string('packaging')->nullable();
    $table->integer('min_order_quantity')->default(1);
    $table->integer('reorder_level')->default(10);
    $table->foreignId('category_id')->constrained();
    $table->foreignId('subcategory_id')->nullable()->constrained();
    $table->boolean('is_active')->default(true);
    $table->softDeletes();
    $table->timestamps();
    
    $table->fullText(['name', 'description']);
});
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};


