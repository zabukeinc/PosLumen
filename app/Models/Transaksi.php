<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model {
    protected $table_name = 'transaksis';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = array('id_produk','id_user','jml_transaksi','total_harga');


}

?>