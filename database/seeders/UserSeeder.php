<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk menambahkan data pengguna.
     *
     * @return void
     */
    public function run()
    {
        $email = 'johndoe@example.com';

        // Cek apakah email sudah a da di tabel users
        if (User::whereEmail($email)->exists()) {
            $this->command->info("Email {$email} sudah ada di tabel users, skipping...");
        } else {
            User::create([
                'name' => 'John Doe',
                'phone' => '08123456789',
                'profile_picture' => null,  // Kosongkan jika tidak ada foto profil
                'email' => $email,
                'password' => bcrypt('password123'), // Gunakan bcrypt untuk enkripsi password
            ]);

            $this->command->info("Berhasil menambahkan user {$email}.");
        }
    }
}

