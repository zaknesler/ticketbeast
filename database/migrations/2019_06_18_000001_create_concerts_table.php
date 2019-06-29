<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConcertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concerts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');

            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->timestamp('date');
            $table->integer('ticket_price');
            $table->integer('ticket_quantity');
            $table->string('venue');
            $table->string('venue_address');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->text('additional_information')->nullable();

            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('concerts');
    }
}
