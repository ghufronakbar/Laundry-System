<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel users.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();  // Menggunakan UUID sebagai primary key
            $table->string('name');         // Nama pengguna
            $table->string('phone');        // Nomor telepon pengguna
            $table->string('profile_picture')->nullable();  // Foto profil (nullable)
            $table->string('email')->unique();  // Email pengguna (unik)
            $table->string('password');     // Password pengguna
            $table->timestamps();           // Created at & Updated at
        });
    }

    /**
     * Membalikkan migrasi untuk menghapus tabel users.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
