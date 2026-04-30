<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stock_adjustments')) {
            Schema::create('stock_adjustments', function (Blueprint $table) {
                $table->id();
                $table->integer('product_id');
                $table->integer('employee_id');
                $table->integer('quantity');
                $table->enum('reason', ['damaged', 'lost', 'expired', 'correction']);
                $table->string('notes', 255)->nullable();
                $table->date('adjustment_date');
                $table->timestamps();
            });

            DB::statement('ALTER TABLE stock_adjustments ADD CONSTRAINT stock_adjustments_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(id)');
            DB::statement('ALTER TABLE stock_adjustments ADD CONSTRAINT stock_adjustments_employee_id_foreign FOREIGN KEY (employee_id) REFERENCES employees(id)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};