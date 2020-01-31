<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model {
    protected $table_name = 'suppliers';
    protected $primaryKey = 'id_supplier';

    protected $fillable = array('nama_supplier');


}

?>