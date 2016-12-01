<?php

use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function($table)
        {
            $table->increments('id');
            $table->integer('user_id');            
            $table->integer('room_id');
            $table->integer('registered');
            $table->timestamp('last_activity');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sessions');
    }

}