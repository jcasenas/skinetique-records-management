<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('product_id');
            $table->integer('employee_id');
            $table->integer('quantity');
            $table->string('reason', 255)->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0.00);
            $table->date('return_date');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE returns ADD CONSTRAINT returns_order_id_foreign FOREIGN KEY (order_id) REFERENCES orders(id)');
        DB::statement('ALTER TABLE returns ADD CONSTRAINT returns_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(id)');
        DB::statement('ALTER TABLE returns ADD CONSTRAINT returns_employee_id_foreign FOREIGN KEY (employee_id) REFERENCES employees(id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};