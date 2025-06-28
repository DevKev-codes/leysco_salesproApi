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
     Schema::create('warehouses', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique(); // e.g., NCW
    $table->string('name');
    $table->string('type');
    $table->text('address');
    $table->string('manager_email')->nullable();
    $table->string('phone')->nullable();
    $table->integer('capacity')->default(0);
    $table->decimal('latitude', 10, 6)->nullable();
    $table->decimal('longitude', 10, 6)->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
