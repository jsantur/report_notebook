<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $fillable = [
        'fecha',
        'hora',
        'turno',
        'supervisor_campo_id',
        'supervisor_camaras_id',
        'ocurrencias_relevo',
        'distribucion_personal_camaras',
        'distribucion_personal_campo',
        'reporte_personal_patrullando',
        'visualizaciones_resaltantes',
        'supervisores_camaras',
        'user_id',
        'unidades_reportes',
    ];

    protected $casts = [
        'supervisores_camaras' => 'array',
        'unidades_reportes' => 'array',
        'distribucion_personal_camaras' => 'array',
        'distribucion_personal_campo' => 'array',
        'reporte_personal_patrullando' => 'array',
        'visualizaciones_resaltantes' => 'array',
    ];

    protected $appends = ['supervisores_camaras_list'];

    public function supervisorCampo()
    {
        return $this->belongsTo(Serenazgo::class, 'supervisor_campo_id');
    }

    public function supervisorCamaras()
    {
        return $this->belongsTo(Serenazgo::class, 'supervisor_camaras_id');
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSupervisoresCamarasListAttribute()
    {
        if (!empty($this->supervisores_camaras) && is_array($this->supervisores_camaras)) {
            return Serenazgo::whereIn('id', $this->supervisores_camaras)->get();
        } elseif ($this->supervisor_camaras_id) {
            $supervisor = $this->supervisorCamaras;
            return $supervisor ? collect([$supervisor]) : collect([]);
        }
        return collect([]);
    }
}
