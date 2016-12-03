<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('user_name');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->integer('sub_id')->unsigned();
            $table->foreign('sub_id')
                ->references('id')->on('subs')
                ->onDelete('cascade');
            $table->integer('thread_id')->unsigned();
            $table->foreign('thread_id')
                ->references('id')->on('threads')
                ->onDelete('cascade');
            $table->timestamps();
            $table->text('comment_text')->nullable();
            $table->integer('up_votes')->unsigned();
            $table->integer('down_votes')->unsigned();
            $table->integer('total_votes');

            $table->integer('comment_ref_id')->unsigned()->nullable();
            $table->foreign('comment_ref_id')
                ->references('id')->on('comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comments');

    }
}
