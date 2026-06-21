<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteDraft extends Model
{
    protected $table = 'reporte_drafts';

    protected $fillable = [
        'user_id',
        'turno',
        'fecha',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sincroniza los kilometrajes desde asignacion_temps hacia el JSON de reporte_drafts
     * para que la UI principal del reporte reciba la actualización en tiempo real.
     */
    public static function syncKilometrajes($userId)
    {
        $draft = self::where('user_id', $userId)->first();
        if (!$draft || !isset($draft->data)) {
            return false;
        }

        $data = $draft->data;
        $asignaciones = AsignacionTemp::where('user_id', $userId)->where('tipo', 'vehicular')->get();

        if ($asignaciones->isEmpty()) {
            return false;
        }

        $updated = false;

        if (isset($data['patrullajeAutomatico']) && is_array($data['patrullajeAutomatico'])) {
            foreach ($data['patrullajeAutomatico'] as &$unidadData) {
                // Alpine usually stores 'nombre' or 'unidad_id' for vehicles
                $unidadName = $unidadData['nombre'] ?? ($unidadData['unidad'] ?? '');
                $asig = $asignaciones->firstWhere('unidad_id', $unidadName);
                if ($asig) {
                    $unidadData['km'] = $asig->km;
                    $unidadData['ap'] = $asig->ap;
                    $unidadData['po'] = $asig->po;
                    $updated = true;
                }
            }
        }

        if (isset($data['selectedCampo']) && is_array($data['selectedCampo'])) {
            foreach ($data['selectedCampo'] as &$campo) {
                if (isset($campo['tipo_patrullaje']) && $campo['tipo_patrullaje'] === 'Vehicular') {
                    $unidadName = $campo['unidad'] ?? '';
                    $asig = $asignaciones->firstWhere('unidad_id', $unidadName);
                    if ($asig) {
                        $campo['km'] = $asig->km;
                        $campo['ap'] = $asig->ap;
                        $campo['po'] = $asig->po;
                        $updated = true;
                    }
                }
            }
        }

        if ($updated) {
            $data['lastSaved'] = round(microtime(true) * 1000);
            $data['last_modified_by'] = 'system'; // Don't trigger admin change notifications for kilometraje syncs
            $draft->data = $data;
            $draft->save();
            return true;
        }

        return false;
    }
}
