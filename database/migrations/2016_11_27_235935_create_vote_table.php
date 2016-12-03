<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('vote_type');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->integer('thread_id')->unsigned()->nullable();
            $table->foreign('thread_id')
                ->references('id')->on('threads')
                ->onDelete('cascade');

            $table->integer('comment_id')->unsigned()->nullable();
            $table->foreign('comment_id')
                ->references('id')->on('comments')
                ->onDelete('cascade');
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
        Schema::drop('votes');
    }
}
