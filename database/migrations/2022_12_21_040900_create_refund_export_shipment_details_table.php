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
        Schema::create('refund_export_shipment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refund_export_shipment_id');
            $table->foreign('refund_export_shipment_id')->references('id')->on('refund_export_shipments');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->integer('quantity');
            $table->float('export_price');
            $table->float('refund_price')->nullable();
            $table->string('lot_code')->nullable();
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
        Schema::dropIfExists('refund_export_shipment_details');
    }
};
