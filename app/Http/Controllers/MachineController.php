<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index()
    {
        $washing_machine = Machine::where('name', 'WASHING')->first();
        $drying_machine = Machine::where('name', 'DRYING')->first();

        return view('machine', compact('washing_machine', 'drying_machine'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'washing_machine_total' => 'required|numeric',
            'drying_machine_total' => 'required|numeric',
            'washing_machine_price' => 'required|numeric',
            'drying_machine_price' => 'required|numeric',
        ]);

        Machine::where('name', 'WASHING')->update([
            'total_machine' => $request->washing_machine_total,
            'price' => $request->washing_machine_price,
        ]);

        Machine::where('name', 'DRYING')->update([
            'total_machine' => $request->drying_machine_total,
            'price' => $request->drying_machine_price,
        ]);

        return redirect()->route('machine.index')->with('success', 'Data berhasil diperbarui');
    }
}
