<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel reservations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Menggunakan UUID sebagai primary key
            $table->uuid('user_id');       // FK ke tabel users
            $table->uuid('machine_id');    // FK ke tabel machines
            $table->integer('machine_number'); // Nomor mesin yang dipesan
            $table->integer('total');      // Total harga untuk reservasi
            $table->string('payment_method')->nullable(); // Metode pembayaran (nullable)
            $table->string('snap_token')->nullable(); // Token untuk payment gateway (nullable)
            $table->enum('status', ['PENDING', 'EXPIRED', 'CANCELLED', 'PAID']); // Status reservasi
            $table->timestamp('reservation_date'); // Tanggal dan waktu reservasi
            $table->timestamps();            // Created at & Updated at

            // Menambahkan foreign key untuk user_id dan machine_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('machine_id')->references('id')->on('machines')->onDelete('cascade');
        });
    }

    /**
     * Membalikkan migrasi untuk menghapus tabel reservations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
