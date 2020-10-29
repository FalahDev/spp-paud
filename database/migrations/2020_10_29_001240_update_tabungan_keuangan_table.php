<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTabunganKeuanganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Schema::table('tabungan', function (Blueprint $table) {
            $table->unsignedBigInteger('siswa_id')->change();
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
        });
        Schema::table('keuangan', function (Blueprint $table) {
            $table->unsignedBigInteger('tabungan_id')->change();
            $table->foreign('tabungan_id')->references('id')->on('tabungan')->onDelete('cascade');
            $table->unsignedBigInteger('transaksi_id')->change();
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
        Schema::table('tabungan', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
        });
        Schema::table('keuangan', function (Blueprint $table) {
            $table->dropForeign(['tabungan_id']);
            $table->dropForeign(['transaksi_id']);
        });
    }
}
