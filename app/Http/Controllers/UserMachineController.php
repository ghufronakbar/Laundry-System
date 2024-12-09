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

        foreach ($drying_machines as &$drying_machine) {
            $drying_machine['name'] = 'Mesin ' . $drying_machine['machine_number'];
            unset($drying_machine['machine_number']);
            // unset($drying_machine['type']);
        }

        foreach ($washing_machines as &$washing_machine) {
            $washing_machine['name'] = 'Mesin ' . $washing_machine['machine_number'];
            unset($washing_machine['machine_number']);
            // unset($washing_machine['type']);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Data mesin',
            'data' => [
                // 'current_time' => $currentTime,
                // 'reservations' => $reservations,
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

    public function show(Request $request, $params)
    {
        try {

            if ($params !== 'WASHING' && $params !== 'DRYING') {
                return response()->json([
                    'status' => 400,
                    'message' => 'Bad Request',
                ], 400);
            }
            if (!$request->query('date')) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Harus memiliki query date',
                ], 400);
            }


            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $request->query('date'))) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Query date harus dalam format YYYY-MM-DD',
                ], 400);
            }

            if (!$request->query('machine_number')) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Harus memiliki query machine_number',
                ], 400);
            }

            if (!is_numeric($request->query('machine_number'))) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Query machine_number harus angka',
                ], 400);
            }

            if ($request->query('machine_number') < 1) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Query machine_number harus lebih besar dari 0',
                ], 400);
            }

            // Generate daftar waktu setengah jam (00:00, 00:30, 01:00, dst.)
            $times = [];
            for ($i = 0; $i < 24; $i += 0.5) {
                $times[] = sprintf('%02d:%02d', floor($i), round(60 * ($i - floor($i))));
            }

            // Inisialisasi array untuk menandai ketersediaan
            $filteredAvailable = [];
            foreach ($times as $time) {
                $filteredAvailable[] = [
                    'time' => $time,
                    'is_available' => true,  // Asumsikan semua waktu tersedia dulu
                ];
            }

            $checkMachine = Machine::where('name', $params)
                ->where('total_machine', '>=', $request->query('machine_number'))
                ->select('id')
                ->first();

            if (!$checkMachine) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Tidak ada data',
                ], 400);
            }

            // Ambil data reservasi untuk tanggal dan mesin yang diminta
            $date = Carbon::parse($request->query('date'));
            $reservations = Reservation::whereBetween('reservation_date', [
                $date->startOfDay()->format('Y-m-d\TH:i:s.u\Z'),
                $date->endOfDay()->format('Y-m-d\TH:i:s.u\Z'),
            ])
                ->where('machine_number', $request->query('machine_number'))
                ->join('machines', 'reservations.machine_id', '=', 'machines.id')
                ->where('machines.name', $params)
                ->whereIn('reservations.status', ['PAID', 'PENDING'])
                ->select('reservations.*', 'machines.name as machine_name')
                ->get();

            $currentDate = Carbon::now()->format('Y-m-d\TH:i:s.u\Z');
            $currentTime = Carbon::parse($currentDate)->format('H:i');
            // return Carbon::parse($currentTime)->format('H:i');
            // Loop melalui setiap reservasi dan sesuaikan waktu yang terpengaruh
            $formattedCurDate = Carbon::parse($currentDate)->format('Y-m-d');
            foreach ($reservations as $reservation) {
                // Ambil waktu dari reservation_date, hanya bagian hh:mm
                $reservationTime = Carbon::parse($reservation->reservation_date)->format('H:i');
                // $reservationDate = Carbon::parse($reservation->reservation_date)->format('Y-m-d');

                // Cari waktu yang cocok di array filteredAvailable dan tandai sebagai tidak tersedia
                foreach ($filteredAvailable as &$timeSlot) {
                    if ($timeSlot['time'] === $reservationTime || $currentTime >= $timeSlot['time']) {
                        $timeSlot['is_available'] = false;
                    }
                    // if ($request->query('date') < $formattedCurDate) {
                    //     $timeSlot['is_available'] = false;
                    // }
                }
            }

            // Jika current date lebih tinggi maka semua is_available false
            // if (Carbon::parse($request->query('date'))->gt(Carbon::now()->format('Y-m-d'))) {
            //     foreach ($filteredAvailable as &$timeSlot) {
            //         $timeSlot['is_available'] = false;
            //     }
            // }

            $morning = [];
            $afternoon = [];
            $night = [];

            foreach ($filteredAvailable as $timeSlot) {
                if (Carbon::parse($timeSlot['time'])->hour >= 3 && Carbon::parse($timeSlot['time'])->hour < 12) {
                    $morning[] = $timeSlot;
                } elseif (Carbon::parse($timeSlot['time'])->hour >= 12 && Carbon::parse($timeSlot['time'])->hour < 18) {
                    $afternoon[] = $timeSlot;
                } elseif (Carbon::parse($timeSlot['time'])->hour >= 18 && Carbon::parse($timeSlot['time'])->hour < 24) {
                    $night[] = $timeSlot;
                } elseif (Carbon::parse($timeSlot['time'])->hour >= 0 && Carbon::parse($timeSlot['time'])->hour < 3) {
                    $night[] = $timeSlot;
                }
            }

            $data = [
                'morning' => $morning,
                'afternoon' => $afternoon,
                'night' => $night
            ];

            // Kembalikan hasil filteredAvailable
            return response()->json([
                'status' => 200,
                'message' => 'Data ketersediaan mesin',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
