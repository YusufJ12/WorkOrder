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
        Schema::create('work_order_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->integer('previous_status');
            $table->integer('new_status');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->text('stage_note')->nullable();
            $table->integer('quantity_processed')->default(0);
            $table->integer('reject_quantity')->default(0);
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('work_order_id')->references('id')->on('work_orders')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_order_logs');
    }
};
