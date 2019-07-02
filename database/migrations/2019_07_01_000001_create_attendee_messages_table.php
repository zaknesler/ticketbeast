<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendeeMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendee_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('concert_id')->unsigned()->index();

            $table->string('subject');
            $table->text('body');
            $table->timestamps();

            $table->foreign('concert_id')->references('id')->on('concerts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendee_messages');
    }
}
