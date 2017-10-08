<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->integer('status')->default(0);
            $table->string('area')->nullable();
            $table->integer('fav')->default(0); //0未收藏， 1已收藏
            $table->float('macd_max', 8, 5)->nullable(); //macd历史最大值，用于策略参考设值
            $table->float('macd_cur', 8, 5)->nullable(); //当天macd值， 用于值报警
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
        Schema::dropIfExists('stocks');
    }
}
