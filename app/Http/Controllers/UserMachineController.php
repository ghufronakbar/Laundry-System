<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class UserMachineController extends Controller
{
    public function index(Request $request)
    {
        $currentTime = Carbon::now()->format('Y-m-d\TH:i:s.u') . 'Z';

        $washing_machine = Machine::where('name', 'WASHING')->first();
        $drying_machine = Machine::where('name', 'DRYING')->first();
        $reservations = Reservation::leftJoin('machines', 'reservations.machine_id', '=', 'machines.id')
            ->select('reservations.*', 'machines.name as machine_name')
            ->get();

        $washing_machines = [];
        if ($washing_machine) {
            for ($i = 1; $i <= $washing_machine->total_machine; $i++) {
                $isAvailable = $this->checkMachineAvailability($reservations, $washing_machine->id, $i, $currentTime);
                $washing_machines[] = [
                    'name' => 'WASHING ' . $i,
                    'type' => 'WASHING',
                    'machine_number' => $i,
                    'is_available' => $isAvailable
                ];
            }
        }

        $drying_machines = [];
        if ($drying_machine) {
            for ($i = 1; $i <= $drying_machine->total_machine; $i++) {
                // Periksa ketersediaan mesin menggunakan waktu Indonesia
                $isAvailable = $this->checkMachineAvailability($reservations, $drying_machine->id, $i, $currentTime);
                $drying_machines[] = [
                    'name' => 'DRYING ' . $i,
                    'type' => 'DRYING',
                    'machine_number' => $i,
                    'is_available' => $isAvailable
                ];
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Data mesin',
            'data' => [
                'current_time' => $currentTime,
                'reservations' => $reservations,
                'washing_machines' => $washing_machines,
                'drying_machines' => $drying_machines,
            ]
        ]);
    }

    /**
     * Fungsi untuk mengecek ketersediaan mesin berdasarkan reservasi dan waktu sekarang
     *
     * @param  Collection  $reservations
     * @param  string  $machineId
     * @param  int  $machineNumber
     * @param  Carbon  $currentTime
     * @return bool
     */

    private function checkMachineAvailability($reservations, $machineId, $machineNumber, $currentTime)
    {
        // Log current time untuk memeriksa zona waktu dan format
        Log::info('Current Time: ' . $currentTime);

        // Ambil semua reservasi untuk mesin yang dimaksud
        $reservationsForMachine = $reservations->filter(function ($reservation) use ($machineId, $machineNumber) {
            return $reservation->machine_id == $machineId && $reservation->machine_number == $machineNumber;
        })->filter(function ($reservation) {
            return in_array($reservation->status, ['PAID', 'PENDING']);
        });

        // Log reservasi yang ditemukan
        if ($reservationsForMachine->isEmpty()) {
            Log::info("No reservations found for Machine {$machineNumber}.");
        } else {
            Log::info("Found reservations for Machine {$machineNumber}: " . $reservationsForMachine->count());
        }

        // Pastikan currentTime adalah objek Carbon, bukan string
        $currentTime = Carbon::parse($currentTime);  // Menggunakan objek Carbon untuk currentTime
        Log::info('Parsed Current Time: ' . $currentTime->toDateTimeString());

        // Periksa setiap reservasi
        foreach ($reservationsForMachine as $reservation) {
            // Pastikan reservation_date dan reservation_end adalah objek Carbon
            $reservationStart = Carbon::parse($reservation->reservation_date);
            $reservationEnd = Carbon::parse($reservation->reservation_end);

            // Log untuk memverifikasi waktu reservasi
            Log::info('Reservation Start: ' . $reservationStart->toDateTimeString());
            Log::info('Reservation End: ' . $reservationEnd->toDateTimeString());

            // Periksa jika waktu sekarang berada dalam rentang waktu reservasi
            if ($currentTime->greaterThanOrEqualTo($reservationStart) && $currentTime->lessThanOrEqualTo($reservationEnd)) {
                Log::info("Machine {$machineNumber} is NOT available - Current Time is within reservation period.");
                return false;  // Mesin tidak tersedia jika sekarang berada dalam periode reservasi
            }
        }

        Log::info("Machine {$machineNumber} is available.");
        return true;  // Mesin tersedia jika tidak ada reservasi yang aktif saat ini
    }
}
