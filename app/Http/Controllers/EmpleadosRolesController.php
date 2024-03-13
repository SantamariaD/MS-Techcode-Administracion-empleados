<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmpleadoRoles;
use App\Models\Empleado;
use App\Models\EmpleadoLaborales;
use App\Models\Rol;
use App\Models\RolPermisos;
use App\Respuestas\Respuestas;
use Illuminate\Support\Facades\Validator;

class EmpleadosRolesController extends Controller
{
    public function consultarEmpleadoRoles($id)
    {
        if (!isset($id)) {
            return response()->json(
                Respuestas::respuesta400('No se tiene el id del empleado.'),
                400
            );
        }

        $empleado = Empleado::where('id_user', $id)->first();
        $empleadoLaboral = EmpleadoLaborales::where('idEmpleado', $empleado['id'])->first();

        $empleadoRoles = EmpleadoRoles::where('idEmpleado', $empleado['id'])
            ->join('roles', 'roles.id', '=', 'empleados_roles.idRol')
            ->select('empleados_roles.*', 'roles.nombre AS nombreRol')
            ->get();

        $respuestaPermisos = [];

        foreach ($empleadoRoles as $empleadoRol) {
            $permisos = RolPermisos::where('idRol', $empleadoRol['idRol'])
                ->join('permisos', 'permisos.id', '=', 'roles_permisos.idPermiso')
                ->select('roles_permisos.*', 'permisos.nombre AS nombrePermiso')
                ->get();

            array_push($respuestaPermisos, ...$permisos);
        }

        $respuesta = [
            "roles" => $empleadoRoles,
            "permisos" => $respuestaPermisos,
            "idEmpleado" => $empleado["id"],
            "idSucursal" => $empleadoLaboral["sucursal_id"]
        ];

        if ($empleado["baja"])
            $respuesta["baja"] = true;

        return response()->json(
            Respuestas::respuesta200(
                'Consulta exitosa.',
                $respuesta
            )
        );
    }

    public function consultarRoles()
    {
        $roles = Rol::all();

        return response()->json(
            Respuestas::respuesta200(
                'Consulta exitosa.',
                $roles
            )
        );
    }

    public function consultarEmpleadosRoles()
    {
        $empleadoRoles = EmpleadoRoles::join('roles', 'roles.id', '=', 'empleados_roles.idRol')
            ->select('empleados_roles.*', 'roles.nombre AS nombreRol')
            ->get();

        return response()->json(
            Respuestas::respuesta200(
                'Consulta exitosa.',
                $empleadoRoles
            )
        );
    }

    public function guardarEmpleadosRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(
                Respuestas::respuesta400($validator->errors(), 400)
            );
        }

        $roles = $request->roles;

        foreach ($roles as $rol) {
            $datosEntrada = [
                'idRol' => $rol['idRol'],
                'idEmpleado' => $rol['idEmpleado'],
            ];

            EmpleadoRoles::create($datosEntrada);
        }

        return $this->consultarEmpleadosRoles();
    }

    public function eliminarEmpleadoRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required|array',
        ]);

        if ($validator->fails())
            return response()->json(Respuestas::respuesta400($validator->errors()), 400);

        $roles = $request->roles;

        foreach ($roles as $rol) {
            EmpleadoRoles::where('idRol', $rol['idRol'])
                ->where('idEmpleado', $rol['idEmpleado'])
                ->delete();
        }

        return $this->consultarEmpleadosRoles();
    }
}
