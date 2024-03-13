<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpleadoRoles extends Model
{
    use HasFactory;

    protected $table = 'empleados_roles';

    protected $fillable = [
        'idEmpleado',
        'idRol',
    ];
}
