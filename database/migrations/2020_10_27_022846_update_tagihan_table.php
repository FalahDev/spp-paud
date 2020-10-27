<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTagihanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->unsignedBigInteger('periode_id')->nullable();
            $table->unsignedBigInteger('kelas_id')->change();
            $table->foreign('periode_id')->references('id')->on('periode')->onDelete('cascade');
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
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropForeign(['kelas_id']);
        });
        if (Schema::hasColumns('tagihan', ['periode_id'])) {
            Schema::table('tagihan', function (Blueprint $table) {
                $table->dropColumn(['periode_id']);
            });
        }
    }
}
