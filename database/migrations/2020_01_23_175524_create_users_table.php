<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            Schema::dropIfExists('users');

            $table->bigIncrements('id_users');
            $table->string('username');
            $table->string('password');
            $table->enum('level', array('admin','pegawai'))->default('pegawai');
            $table->integer('id_pegawai')->references('id_account')->on('account');


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
        Schema::dropIfExists('users');

    }
}
