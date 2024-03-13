<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteEmpleado extends Model
{
    use HasFactory;
    protected $table = 'reportes_empleado';
    
    
    protected $fillable = [
        'id_empleado',
        'id_nomina',
        'fecha',
        'id_horario',
        'id_registro',
        'sueldo_dia',
        'hora_extra',
        'comisiones',
        'otros_ganancia',
        'total_ganancia',
        'hora_descontada',
        'adelantos',
        'otros_descuento',
        'total_descuento',
        'comentarios',
        'total'
    ];
}
