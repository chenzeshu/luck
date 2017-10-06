<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMyTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_times', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('stock_id');
            $table->timestamp('refertime');
            $table->integer('tem_id');//短信模板id
            $table->text('msg')->nullable();  //提醒内容（站内提醒， 邮件等也可以用）
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
        Schema::dropIfExists('my_times');
    }
}
