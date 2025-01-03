<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cari user berdasarkan email
        $email = "johndoe@example.com";
        $user = User::whereEmail($email)->first();

        // Pastikan user ditemukan
        if (!$user) {
            $this->command->error("User with email {$email} not found.");
            return;
        }

        // Cari mesin berdasarkan nama
        $washing = Machine::whereName('WASHING')->first();
        $drying = Machine::whereName('DRYING')->first();

        // Pastikan mesin ditemukan
        if (!$washing) {
            $this->command->error("Machine with name 'WASHING' not found.");
            return;
        }

        if (!$drying) {
            $this->command->error("Machine with name 'DRYING' not found.");
            return;
        }

        // Buat data reservation
        $reservation1 = Reservation::create([
            'user_id' => $user->id,
            'machine_id' => $washing->id,
            'machine_number' => 4,  // Misal pilih mesin nomor 1
            'status' => 'PAID',  // Status yang valid
            'reservation_date' => '2024-12-07T16:30:00.000000z',
            'reservation_end' => '2024-12-07T17:00:00.000000z',
            'total' => $washing->price,  // Pastikan total diisi dengan harga mesin
        ]);

        Payment::create([
            'id' => (string) Str::uuid(), // Generate UUID untuk kolom id
            'reservation_id' => $reservation1->id, // Menyimpan ID reservation yang baru dibuat
            'total' => $washing->price,  // Total pembayaran
            'payment_method' => 'QRIS',
            'snap_token' => 'somerandomstringxxx', // Token pembayaran (dapat diubah)
            'paid_at' => '2024-12-07T11:00:00.000000z',  // Waktu pembayaran dalam format ISO 8601
        ]);

        $reservation2 = Reservation::create([
            'user_id' => $user->id,
            'machine_id' => $drying->id,
            'machine_number' => 2,  // Misal pilih mesin nomor 1
            'status' => 'PAID',  // Status yang valid
            'reservation_date' => '2024-12-07T17:00:00.000000z',
            'reservation_end' => '2024-12-07T17:30:00.000000z',
            'total' => $drying->price,  // Pastikan total diisi dengan harga mesin
        ]);

        // Buat data payment yang terkait dengan reservation yang baru dibuat

        Payment::create([
            'id' => (string) Str::uuid(), // Generate UUID untuk kolom id
            'reservation_id' => $reservation2->id, // Menyimpan ID reservation yang baru dibuat
            'total' => $drying->price,  // Total pembayaran
            'payment_method' => 'BNI',
            'snap_token' => 'somerandomstringxxx', // Token pembayaran (dapat diubah)
            'paid_at' => '2024-12-07T11:00:00.000000z',  // Waktu pembayaran dalam format ISO 8601
        ]);
    }
}
