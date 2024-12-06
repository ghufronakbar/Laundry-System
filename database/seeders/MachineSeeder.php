<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Machine;

class MachineSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk menambahkan data mesin.
     *
     * @return void
     */
    public function run()
    {
        // Seeder untuk mesin WASHING
        if (!Machine::whereName('WASHING')->exists()) {
            Machine::create([
                'name' => 'WASHING',
                'total_machine' => 6,
                'price' => 20000,
            ]);
            echo "Seeder untuk mesin WASHING berhasil dibuat.\n";
        } else {
            echo "Seeder untuk mesin WASHING sudah ada, tidak perlu dibuat.\n";
        }

        // Seeder untuk mesin DRYING
        if (!Machine::whereName('DRYING')->exists()) {
            Machine::create([
                'name' => 'DRYING',
                'total_machine' => 6,
                'price' => 20000,
            ]);
            echo "Seeder untuk mesin DRYING berhasil dibuat.\n";
        } else {
            echo "Seeder untuk mesin DRYING sudah ada, tidak perlu dibuat.\n";
        }
    }
}
