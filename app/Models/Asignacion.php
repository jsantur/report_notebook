<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    protected $table = 'asignaciones';

    protected $touches = ['reporte'];

    protected $fillable = [
        'reporte_id',
        'unidad_id',
        'tipo',
        'subtipo',
        'placa',
        'sector',
        'turnos',
        'jurisdiccion',
        'km',
        'ap',
        'po',
        'cod_po',
    ];

    public function reporte()
    {
        return $this->belongsTo(Reporte::class);
    }
}
