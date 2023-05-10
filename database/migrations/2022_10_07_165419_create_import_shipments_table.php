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
        Schema::create('import_shipments', function (Blueprint $table) {
            $table->id();
            $table->timestamp('import_date');
            $table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->integer('quantity');
            $table->string('import_code');
            $table->integer('import_type');
            $table->integer('payment');
            $table->string('description')->nullable();
            $table->float('import_price_totail');
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
        Schema::dropIfExists('import_shipments');
    }
};

