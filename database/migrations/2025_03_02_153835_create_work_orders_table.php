<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->char('work_order_number', 10)->unique();
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->integer('quantity_final')->default(0);
            $table->date('production_deadline');
            $table->char('status', 1)->comment('1 = Pending, 2 = Pemotongan In Progress, 3 = Perakitan In Progress, 4 = Completed, 5 = Canceled');
            $table->string('note', 255)->nullable();
            $table->unsignedBigInteger('operator_id');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('operator_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_orders');
    }
};
