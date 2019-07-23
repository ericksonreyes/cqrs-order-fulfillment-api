<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('rowId');
            $table->string('id', 150)->unique();
            $table->string('status', 150);
            $table->string('customerId', 150)->index();
            $table->string('shipper', 150)->nullable();
            $table->string('trackingId', 150)->nullable();
            $table->string('cancellationReason', 255)->nullable();
            $table->integer('dateShipped')->nullable();
            $table->integer('postedOn');
            $table->integer('lastUpdatedOn')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
