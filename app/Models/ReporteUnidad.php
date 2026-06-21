<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteUnidad extends Model
{
    protected $table = 'reporte_unidades';

    protected $fillable = [
        'unidad',
        'placa',
        'km',
        'ap',
        'po',
        'fecha',
        'turno',
    ];
}
