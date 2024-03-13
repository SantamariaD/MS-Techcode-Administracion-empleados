<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpleadoLaborales extends Model
{
    use HasFactory;
    protected $table = 'empleados_laborales';

    protected $fillable = [
        'idEmpleado',
        'area_id',
        'puesto_id',
        'sucursal_id',
        'idCatalogoBanco',
        'idJefeInmediato',
        'salario',
        'tipoContrato',
        'clabeInterbancaria',
        'periodoPago',
        'fechaAlta',
        'fechaBaja',
        'fecha_reingreso',
        'horas_laborales',
    ];
}
