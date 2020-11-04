<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSiswaKelasRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('siswa', 'kelas_id')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->dropColumn('kelas_id');
            });
        }
        Schema::table('siswa', function (Blueprint $table) {
            $table->unsignedBigInteger('kelas_id')->nullable();

            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
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
            $table->dropForeign(['kelas_id']);
        });
    }
}
