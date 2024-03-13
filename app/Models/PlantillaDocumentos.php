<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaDocumentos extends Model
{
    use HasFactory;
    protected $table = 'plantilla_documentos';
    

    protected $fillable = [
        'nombreDocumento',
        'especificaciones',
    ];
}