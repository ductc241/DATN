<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_export_shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('export_shipment_id');
            $table->foreign('export_shipment_id')->references('id')->on('export_shipments');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->integer('refund_totall_quantity');
            $table->string('refund_code');
            $table->integer('refund_type');
            $table->string('description')->nullable();
            $table->float('refund_price_totail');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refund_export_shipments');
    }
};
