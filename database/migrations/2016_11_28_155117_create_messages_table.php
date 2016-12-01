<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message');
            $table->timestamp('time');
            $table->integer('room_id');
            $table->integer('user_id');
            $table->integer('guest_id');
            $table->foreign('room_id')->references('id')->on('Rooms');
            $table->foreign('user_id')->references('id')->on('User');
            $table->foreign('guest_id')->references('id')->on('Guest');
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
        Schema::dropIfExists('messages');
    }
}
