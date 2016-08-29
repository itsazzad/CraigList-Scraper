<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('url_id')->unsigned();
            $table->string('link')->unique();
            $table->string('name')->nullable();
            $table->string('phone', 20);
            $table->string('email', 100);
            $table->string('title');
            $table->text('mapLocation');
            $table->mediumText('body');
            $table->foreign('url_id')
                ->references('id')->on('urls')->onDelete('cascade');
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
        Schema::drop('leads');
    }
}
