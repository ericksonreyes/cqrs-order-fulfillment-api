<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('context_name', 255);
            $table->string('entity_type', 150);
            $table->string('entity_id', 150);
            $table->longText('event_data');
            $table->longText('event_meta_data');
            $table->string('event_hash', 1000)->index();
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
