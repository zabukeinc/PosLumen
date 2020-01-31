<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produks', function (Blueprint $table) {
            Schema::dropIfExists('produk');


            $table->bigIncrements('id_produk');
            $table->string('nama_produk');
            $table->enum('jenis_produk', array('makanan','minuman'))->default('makanan');
            $table->integer('harga_produk');
            $table->integer('stok_produk');
            $table->integer('id_supplier')->references('id_supplier')->on('supplier');


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
        Schema::dropIfExists('produk');

    }
}
