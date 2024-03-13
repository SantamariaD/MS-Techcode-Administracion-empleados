<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolPermisos extends Model
{
    use HasFactory;
    protected $table = 'roles_permisos';
    
    protected $fillable = [
        'idRol',
        'idPermiso'
    ];
}
