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
        $machine = Machine::whereName('WASHING')->first();

        // Pastikan mesin ditemukan
        if (!$machine) {
            $this->command->error("Machine with name 'WASHING' not found.");
            return;
        }

        // Buat data reservation
        $reservation1 = Reservation::create([
            'user_id' => $user->id,
            'machine_id' => $machine->id,
            'machine_number' => 1,  // Misal pilih mesin nomor 1
            'status' => 'PAID',  // Status yang valid
            'reservation_date' => '2024-12-07 00:00:00',
            'total' => $machine->price,  // Pastikan total diisi dengan harga mesin
        ]);

        Payment::create([
            'id' => (string) Str::uuid(), // Generate UUID untuk kolom id
            'reservation_id' => $reservation1->id, // Menyimpan ID reservation yang baru dibuat
            'total' => $machine->price,  // Total pembayaran
            'payment_method' => 'QRIS',
            'snap_token' => 'somerandomstringxxx', // Token pembayaran (dapat diubah)
            'paid_at' => '2024-12-07 00:00:00',  // Waktu pembayaran
        ]);

        $reservation2 = Reservation::create([
            'user_id' => $user->id,
            'machine_id' => $machine->id,
            'machine_number' => 1,  // Misal pilih mesin nomor 1
            'status' => 'PENDING',  // Status yang valid
            'reservation_date' => '2024-12-06 10:30:00',
            'total' => $machine->price,  // Pastikan total diisi dengan harga mesin
        ]);

        // Buat data payment yang terkait dengan reservation yang baru dibuat

        Payment::create([
            'id' => (string) Str::uuid(), // Generate UUID untuk kolom id
            'reservation_id' => $reservation2->id, // Menyimpan ID reservation yang baru dibuat
            'total' => $machine->price,  // Total pembayaran
            'payment_method' => null,
            'snap_token' => 'somerandomstringxxx', // Token pembayaran (dapat diubah)
            'paid_at' => null,  // Waktu pembayaran
        ]);
    }
}
