<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serenazgo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'dni',
        'fecha_nacimiento',
        'celular',
        'perfil_trabajo',
        'nombre_foto',
        'activo',
    ];
}
