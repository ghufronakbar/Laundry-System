<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reservation_id');  // Foreign key ke tabel reservations
            $table->integer('total');
            $table->string('payment_method')->nullable();  // Misalnya untuk MidTrans atau lainnya
            $table->string('snap_token')->nullable();  // Untuk token pembayaran (nullable, menunggu pembayaran)
            $table->timestamp('paid_at')->nullable();  // Menyimpan waktu pembayaran jika sudah berhasil
            $table->timestamps();

            // Menambahkan foreign key constraint
            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
