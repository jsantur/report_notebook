<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehiculo::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('placa', 'like', "%{$search}%")
                  ->orWhere('nro_unidad', 'like', "%{$search}%")
                  ->orWhere('tipo', 'like', "%{$search}%");
        }

        $vehiculos = $query->orderBy('nro_unidad')->paginate(10);

        if ($request->ajax()) {
            return view('vehiculos.partials.list', compact('vehiculos'));
        }

        return view('vehiculos.index', compact('vehiculos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => 'required|unique:vehiculos,placa',
            'tipo' => 'required',
            'nro_unidad' => 'required',
            'tipo_patrullaje' => 'required',
            'descripcion' => 'nullable'
        ]);

        Vehiculo::create($validated);

        return redirect()->route('vehiculos.index')->with('status', 'Vehículo registrado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehiculo $vehiculo)
    {
        $validated = $request->validate([
            'placa' => 'required|unique:vehiculos,placa,' . $vehiculo->id,
            'tipo' => 'required',
            'nro_unidad' => 'required',
            'tipo_patrullaje' => 'required',
            'descripcion' => 'nullable'
        ]);

        $vehiculo->update($validated);

        return redirect()->route('vehiculos.index')->with('status', 'Vehículo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehiculo $vehiculo)
    {
        $vehiculo->delete();

        return redirect()->route('vehiculos.index')->with('status', 'Vehículo eliminado exitosamente.');
    }

    /**
     * Alternar el estado activo/inactivo de un vehículo.
     */
    public function toggleStatus(Vehiculo $vehiculo)
    {
        $vehiculo->activo = !$vehiculo->activo;
        $vehiculo->save();

        $status = $vehiculo->activo ? 'activado' : 'suspendido';
        
        return redirect()->route('vehiculos.index')
            ->with('status', "Vehículo {$vehiculo->nro_unidad} {$status} correctamente.");
    }
}
