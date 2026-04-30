<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->enum('status', ['available', 'unavailable'])->default('unavailable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};