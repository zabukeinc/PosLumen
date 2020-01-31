<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model {
    protected $table_name = 'accounts';

    protected $fillable = array('id_user','nama_pegawai','alamat_pegawai');


}

?>