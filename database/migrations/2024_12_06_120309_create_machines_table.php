<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachinesTable extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel machines.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->uuid('id')->primary();  // UUID sebagai primary key
            $table->string('name');         // Nama mesin (WASHING / DRYING)
            $table->integer('total_machine'); // Total mesin
            $table->integer('price');        // Harga per mesin
            $table->timestamps();           // Created at & Updated at
        });
    }

    /**
     * Membalikkan migrasi untuk menghapus tabel machines.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machines');
    }
}
