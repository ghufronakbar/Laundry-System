<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;

class UserMachineController extends Controller
{
    public function index(Request $request)
    {
        $currentTime = Carbon::now(); // Menggunakan waktu Indonesia

        $washing_machine = Machine::where('name', 'WASHING')->first();
        $drying_machine = Machine::where('name', 'DRYING')->first();
        $reservations = Reservation::get();

        $washing_machines = [];
        if ($washing_machine) {
            for ($i = 1; $i <= $washing_machine->total_machine; $i++) {
                // Periksa ketersediaan mesin menggunakan waktu Indonesia
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

        Log::info('Machine data response', [
            'current_time' => $currentTime->toDateTimeString(),
            'washing_machines' => $washing_machines,
            'drying_machines' => $drying_machines,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Data mesin',
            'data' => [
                'current_time' => $currentTime->toDateTimeString(),
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
        $reservationsForMachine = $reservations->where('machine_id', $machineId)
            ->where('machine_number', $machineNumber)
            ->where('status', 'PAID');

        foreach ($reservationsForMachine as $reservation) {
            if ($currentTime->greaterThanOrEqualTo($reservation->reservation_date) && $currentTime->lessThanOrEqualTo($reservation->reservation_end)) {
                return false;
            }
        }
        return true;
    }
}
