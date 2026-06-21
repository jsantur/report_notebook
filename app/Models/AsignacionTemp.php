<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionTemp extends Model
{
    protected $table = 'asignaciones_temp';

    protected $fillable = [
        'user_id',
        'unidad_id',
        'tipo',
        'subtipo',
        'placa',
        'conductor',
        'sector',
        'turnos',
        'jurisdiccion',
        'km',
        'ap',
        'po',
        'cod_po',
    ];

    protected $casts = [
        'km' => 'decimal:2',
        'ap' => 'integer',
        'po' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
