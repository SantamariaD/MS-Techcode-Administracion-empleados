<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorariosGenerales extends Model
{
    use HasFactory;
    protected $table = 'horarios_generales';
    
    
    protected $fillable = [
        'nombre',
        'horas',
        'hora_entrada',
        'hora_salida',
        'baja'
    ];
}