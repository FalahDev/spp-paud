<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateKekuranganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kekurangan', function (Blueprint $table) {
            $table->unsignedBigInteger('transaksi_id');
            $table->boolean('dibayar')->default(false);

            $table->foreign('transaksi_id')->references('id')->on('transaksi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kekurangan', function (Blueprint $table) {
            $table->dropForeign(['transaksi_id']);
        });
        Schema::table('kekurangan', function (Blueprint $table) {
            $table->removeColumn('transaksi_id');
            $table->removeColumn('dibayar');
        });
    }
}
