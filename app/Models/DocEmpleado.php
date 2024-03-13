<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocEmpleado extends Model
{
    use HasFactory;
    protected $table = 'documentos_empleado';
    
    protected $fillable = [
        'idEmpleado',
        'idPlantillaDocumento',
        'uuid',
        'extension',
        'activo',
        'estatus',
        'comentarios'
    ];
}