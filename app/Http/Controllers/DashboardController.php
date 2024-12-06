<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $count_reservation_washing_today = Reservation::whereDate('reservation_date', date('Y-m-d'))
            ->whereHas('machine', function ($query) {
                $query->where('name', 'WASHING');
            })
            ->count();
        $count_reservation_drying_today = Reservation::whereDate('reservation_date', date('Y-m-d'))
            ->whereHas('machine', function ($query) {
                $query->where('name', 'DRYING');
            })
            ->count();

        $income_monthly = Reservation::whereMonth('reservation_date', date('m'))
            ->where('status', 'PAID')
            ->join('payments', 'reservations.id', '=', 'payments.reservation_id')
            ->sum('payments.total');

        $income_weekly = Reservation::whereBetween('reservation_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', 'PAID')
            ->join('payments', 'reservations.id', '=', 'payments.reservation_id')
            ->sum('payments.total');

        $total_all_transactions = Reservation::count();
        $total_cancelled_transactions = Reservation::where('status', 'CANCELLED')->count();

        $total_washing_machines = Machine::where('name', 'WASHING')->first()->total_machine;
        $total_drying_machines = Machine::where('name', 'DRYING')->first()->total_machine;

        $unavailable_washing_machines = Reservation::whereHas('machine', function ($query) {
            $query->where('name', 'WASHING');
        })
            ->whereBetween('reservation_date', [now()->subMinutes(30), now()])
            ->count();

        $unavailable_drying_machines = Reservation::whereHas('machine', function ($query) {
            $query->where('name', 'DRYING');
        })
            ->whereBetween('reservation_date', [now()->subMinutes(30), now()])
            ->count();


        return view('dashboard', compact('count_reservation_washing_today', 'count_reservation_drying_today', 'income_monthly', 'income_weekly', 'total_all_transactions', 'total_cancelled_transactions', 'total_washing_machines', 'total_drying_machines', 'unavailable_washing_machines', 'unavailable_drying_machines'));
    }
}
