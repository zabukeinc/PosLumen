<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model {
    protected $table_name = 'produks';
    protected $primaryKey = 'id_produk';

    protected $fillable = array('nama_produk','jenis_produk','harga_produk', 'stok_produk', 'id_supplier');


}

?>