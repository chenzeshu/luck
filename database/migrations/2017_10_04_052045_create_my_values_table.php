<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMyValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('favorite_id')->unique();
            $table->float('value',8,5);  //提醒macd值（绝对值)
            $table->string('tem_id');//短信模板id
            $table->text('msg')->nullable();  //提醒内容（站内提醒， 邮件等也可以用）
            $table->integer('known')->default(0); //0:未知， 1：已知（不再提醒）
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
        Schema::dropIfExists('my_values');
    }
}
