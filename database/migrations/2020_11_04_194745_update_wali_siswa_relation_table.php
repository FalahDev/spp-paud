<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWaliSiswaRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('siswa', 'wali_id')) {
            Schema::table('siswa', function(Blueprint $table){
                $table->unsignedBigInteger('wali_id');
                $table->dropColumn('nisn');

                $table->foreign('wali_id')->references('id')->on('wali_siswa')->onDelete('cascade');
            });
            Schema::table('siswa', function(Blueprint $table){
                $table->string('nisn')->unique();
            });
        }
        if(Schema::hasColumn('wali_siswa', 'siswa_id')){
            Schema::table('wali_siswa', function (Blueprint $table) {
                $table->dropForeign(['siswa_id']);
                $table->dropColumn('siswa_id');
            });
        }
        if(!Schema::hasColumn('wali_siswa', 'password')){
            Schema::table('wali_siswa', function (Blueprint $table) {
                $table->string('password');
                $table->rememberToken();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('siswa', 'wali_id')) {
            Schema::table('siswa', function(Blueprint $table){
                $table->dropForeign(['wali_id']);
                $table->dropColumn('wali_id');
            });
        }
        if(!Schema::hasColumn('wali_siswa', 'siswa_id')){
            Schema::table('wali_siswa', function (Blueprint $table) {
                $table->unsignedBigInteger('siswa_id');
            });
        }
    }
}
