<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk menambahkan admin.
     *
     * @return void
     */
    public function run()
    {
        if (!Admin::whereEmail('admin@laundry.com')->exists()) {
            Admin::create([
                'name' => 'Admin Laundry',
                'email' => 'admin@laundry.com',
                'password' => bcrypt('password123'),
            ]);
        } else {
            $this->command->info('Admin with email admin@laundry.com already exists, skipping...');
        }
    }
}

