<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;
    protected $table = 'empleados';

    protected $fillable = [
        'id_user',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'genero',
        'estado_civil',
        'curp',
        'rfc',
        'nss',
        'telefono',
        'correo_electronico',
        'baja',
        'imagen',
        'calle',
        'numeroExt',
        'numeroInt',
        'colonia',
        'codigo_postal',
        'delegacion',
        'ciudad',
        'referencias',
        'descripcion',
    ];
}
