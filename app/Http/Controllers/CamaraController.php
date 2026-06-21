<?php

namespace App\Http\Controllers;

use App\Models\Camara;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CamaraController extends Controller
{
    public function index(Request $request)
    {
        $query = Camara::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('ubicacion', 'like', "%{$search}%");
        }

        $camaras = $query->orderBy('nombre')->paginate(15);

        if ($request->ajax()) {
            return view('camaras.partials.list', compact('camaras'));
        }

        return view('camaras.index', compact('camaras'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:camaras,nombre',
            'ubicacion' => 'nullable'
        ]);

        Camara::create($validated);

        return redirect()->route('camaras.index')->with('status', 'Cámara registrada exitosamente.');
    }

    public function update(Request $request, Camara $camara)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:camaras,nombre,' . $camara->id,
            'ubicacion' => 'nullable'
        ]);

        $camara->update($validated);

        return redirect()->route('camaras.index')->with('status', 'Cámara actualizada exitosamente.');
    }

    public function destroy(Camara $camara)
    {
        $camara->delete();

        return redirect()->route('camaras.index')->with('status', 'Cámara eliminada exitosamente.');
    }

    public function toggleStatus(Camara $camara)
    {
        $camara->activa = !$camara->activa;
        $camara->save();

        $status = $camara->activa ? 'activada' : 'suspendida';
        
        return redirect()->route('camaras.index')
            ->with('status', "Cámara {$camara->nombre} {$status} correctamente.");
    }
}
