<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangJasasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang_jasa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama');
            $table->double('harga_beli')->nullable();
            $table->double('harga_jual');
            $table->integer('stok')->nullable();
            $table->enum('tipe', ['barang', 'jasa']);
            $table->unsignedBigInteger('tagihan_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tagihan_id')->references('id')->on('tagihan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barang_jasa');
    }
}
