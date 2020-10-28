<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSiswaWaliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->unsignedBigInteger('wali_id');

            $table->foreign('wali_id')->references('id')->on('wali_siswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['wali_id']);
        });
        if (Schema::hasColumns('siswa', ['wali_id'])) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->dropColumn('wali_id');
            });
        }
    }
}
