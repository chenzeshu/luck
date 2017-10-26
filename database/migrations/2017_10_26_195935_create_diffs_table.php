<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diffs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('stock_id');
            $table->float('d_diff',8, 5);
            $table->float('w_diff',8, 5);
            $table->float('m_diff',8, 5);
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
        Schema::dropIfExists('diffs');
    }
}
