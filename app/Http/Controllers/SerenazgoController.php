<?php

namespace App\Http\Controllers;

use App\Models\Serenazgo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SerenazgoController extends Controller
{
    /**
     * Definir los cargos permitidos para validación (no se usa aquí, movido a FormRequests)
     */
    protected $allowedRoles = [
        'Supervisor Encargado',
        'Chofer',
        'Operador de Cámaras',
        'Sereno',
        'Supervisor de Cámaras'
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $activo = $request->input('activo');

        $personnel = Serenazgo::query()
            ->when($activo !== null, function ($query) use ($activo) {
                $query->where('activo', $activo);
            })
            ->when($role, function ($query, $role) {
                $roles = is_array($role) ? $role : explode(',', $role);
                $query->where(function($q) use ($roles) {
                    foreach ($roles as $r) {
                        $q->orWhereRaw('LOWER(perfil_trabajo) = ?', [strtolower($r)]);
                    }
                });
            })
            ->when($search, function ($query, $search) {
                $search = \Illuminate\Support\Str::ascii($search);
                $terms = explode(' ', $search);
                
                $query->where(function($q) use ($terms) {
                    foreach ($terms as $term) {
                        if (trim($term) !== '') {
                            $q->where(function($subQ) use ($term) {
                                $subQ->where('nombres', 'LIKE', "%{$term}%")
                                     ->orWhere('apellido_paterno', 'LIKE', "%{$term}%")
                                     ->orWhere('apellido_materno', 'LIKE', "%{$term}%")
                                     ->orWhere('dni', 'LIKE', "%{$term}%")
                                     ->orWhere('perfil_trabajo', 'LIKE', "%{$term}%");
                            });
                        }
                    }
                });
            });

        $personnel = $personnel->latest()->paginate(10);

        if ($request->ajax()) {
            return view('serenazgo.partials.list', compact('personnel'))->render();
        }

        return view('serenazgo.index', compact('personnel'));
    }

    /**
     * Search personnel for JSON response (accessible by all auth users)
     */
    public function searchJson(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $activo = $request->input('activo');

        $personnel = Serenazgo::query()
            ->when($activo !== null, function ($query) use ($activo) {
                $query->where('activo', $activo);
            })
            ->when($role, function ($query, $role) {
                $roles = is_array($role) ? $role : explode(',', $role);
                $query->where(function($q) use ($roles) {
                    foreach ($roles as $r) {
                        $q->orWhereRaw('LOWER(perfil_trabajo) = ?', [strtolower($r)]);
                    }
                });
            })
            ->when($search, function ($query, $search) {
                $search = \Illuminate\Support\Str::ascii($search);
                $terms = explode(' ', $search);
                
                $query->where(function($q) use ($terms) {
                    foreach ($terms as $term) {
                        if (trim($term) !== '') {
                            $q->where(function($subQ) use ($term) {
                                $subQ->where('nombres', 'LIKE', "%{$term}%")
                                     ->orWhere('apellido_paterno', 'LIKE', "%{$term}%")
                                     ->orWhere('apellido_materno', 'LIKE', "%{$term}%")
                                     ->orWhere('dni', 'LIKE', "%{$term}%")
                                     ->orWhere('perfil_trabajo', 'LIKE', "%{$term}%");
                            });
                        }
                    }
                });
            });

        $results = $personnel->orderBy('apellido_paterno', 'asc')
            ->orderBy('apellido_materno', 'asc')
            ->orderBy('nombres', 'asc')
            ->get();

        // Seguridad: Solo administradores pueden ver DNI y Celular en la búsqueda global
        if (auth()->user()->role !== 'admin') {
            $results = $results->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nombres' => $p->nombres,
                    'apellido_paterno' => $p->apellido_paterno,
                    'apellido_materno' => $p->apellido_materno,
                    'perfil_trabajo' => $p->perfil_trabajo,
                ];
            });
        }

        return response()->json($results);
    }

    /**
     * Alternar el estado activo/inactivo del personal.
     */
    public function toggleStatus(Serenazgo $serenazgo)
    {
        $serenazgo->update(['activo' => !$serenazgo->activo]);
        $status = $serenazgo->activo ? 'reactivado' : 'dado de baja';
        return redirect()->back()->with('status', "Personal {$status} correctamente.");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreSerenazgoRequest $request)
    {
        $validated = $request->validated();
        if (empty($validated['celular'])) $validated['celular'] = '000000000';

        // Verificar si el DNI ya existe pero está inactivo (para reactivación)
        $existing = Serenazgo::where('dni', $validated['dni'])->first();
        
        if ($existing && !$existing->activo) {
            $existing->update(array_merge($validated, ['activo' => true]));
            return redirect()->route('serenazgo.index')->with('status', 'Personal reactivado y actualizado exitosamente.');
        }

        Serenazgo::create($validated);

        return redirect()->route('serenazgo.index')->with('status', 'Personal registrado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateSerenazgoRequest $request, Serenazgo $serenazgo)
    {
        $validated = $request->validated();
        if (empty($validated['celular'])) $validated['celular'] = '000000000';

        $serenazgo->update($validated);

        return redirect()->route('serenazgo.index')->with('status', 'Personal actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Serenazgo $serenazgo)
    {
        try {
            $serenazgo->delete();
            return redirect()->route('serenazgo.index')->with('status', 'Personal eliminado permanentemente del sistema.');
        } catch (\Exception $e) {
            return redirect()->route('serenazgo.index')->with('error', 'No se pudo eliminar el personal porque tiene reportes asociados.');
        }
    }
}
