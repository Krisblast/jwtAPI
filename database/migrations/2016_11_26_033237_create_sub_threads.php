<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubThreads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threads', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->integer('sub_id')->unsigned();
            $table->foreign('sub_id')
                ->references('id')->on('subs')
                ->onDelete('cascade');
            $table->timestamps();
            $table->string('title');
            $table->string('image')->nullable();
            $table->string('user_name');
            $table->text('text')->nullable();
            $table->string('link')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('threads');

    }
}
