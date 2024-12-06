<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReservationsTable extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Hapus kolom-kolom terkait pembayaran
            $table->dropColumn(['payment_method', 'snap_token']);
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Tambahkan kembali kolom yang dihapus pada rollback migration
            $table->string('payment_method')->nullable();
            $table->string('snap_token')->nullable();
        });
    }
}
