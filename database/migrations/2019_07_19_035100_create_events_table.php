<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('rowId');
            $table->string('id', 150)->unique();
            $table->string('event_name', 255);
            $table->integer('happened_on');
            $table->string('entity_type', 150);
            $table->string('entity_id', 150);
            $table->longText('event_data');
            $table->index(['entity_id'], 'idx_entity_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
