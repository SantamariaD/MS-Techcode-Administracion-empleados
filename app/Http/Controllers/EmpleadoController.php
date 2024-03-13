<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Respuestas\Respuestas;
use App\Models\Empleado;
use App\Models\EmpleadoRoles;
use App\Models\EmpleadoLaborales;
use Illuminate\Support\Facades\Storage;
use App\Models\DocEmpleado;
use App\Models\RegistroAsistencias;
use Illuminate\Support\Str;
use App\Mail\CorreoController;
use App\Mail\BienvenidaController;
use Illuminate\Support\Facades\Mail;

class EmpleadoController extends Controller
{
    private $UUID;

    public function consultarTodosEmpleados()
    {
        $empleadosRespuesta = $this->consultarEmpleados();

        return response()->json(Respuestas::respuesta200('Empleados encontrados.', $empleadosRespuesta));
    }

    public function crearEmpleado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required|unique:mysql_dinamica.empleados',
            'nombres' => 'required',
            'apellido_paterno' => 'nullable',
            'apellido_materno' => 'nullable',
            'fecha_nacimiento' => 'nullable',
            'genero' => 'nullable',
            'estado_civil' => 'nullable',
            'curp' => 'nullable|unique:mysql_dinamica.empleados',
            'rfc' => 'nullable|unique:mysql_dinamica.empleados',
            'nss' => 'nullable|unique:mysql_dinamica.empleados',
            'telefono' => 'nullable',
            'correo_electronico' => 'nullable|string|email|max:100|unique:mysql_dinamica.empleados',
            'imagen' => 'nullable',
            'calle' => 'nullable',
            'numeroExt' => 'nullable',
            'numeroInt' => 'nullable',
            'colonia' => 'nullable',
            'codigoPostal' => 'nullable',
            'delegacion' => 'nullable',
            'ciudad' => 'nullable',
            'referencias' => 'nullable',
            'contrasena' => 'nullable',
            'uuidBD' => 'nullable',
            'extension' => 'nullable',
            'descripcion' => 'nullable',
            'noEnvioCorreo' => 'nullable',
        ]);

        $empleado = new Empleado();

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()), 400);
        }

        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');
            $UUID = Str::orderedUuid();
            $extension = $archivo->getClientOriginalExtension();
            $empleado->imagen = $UUID;
            $empleado->extension = $extension;

            $archivo->storeAs(
                "/" . $request->uuidBD,
                $UUID . '.' . $extension,
                'empleados'
            );
        }

        $empleado->id_user = $request->id_user;
        $empleado->nombres = $request->nombres;
        $empleado->apellido_paterno = $request->apellido_paterno;
        $empleado->apellido_materno = $request->apellido_materno;
        $empleado->fecha_nacimiento = $request->fecha_nacimiento;
        $empleado->genero = $request->genero;
        $empleado->estado_civil = $request->estado_civil;
        $empleado->curp = $request->curp;
        $empleado->rfc = $request->rfc;
        $empleado->nss = $request->nss;
        $empleado->telefono = $request->telefono;
        $empleado->correo_electronico = $request->correo_electronico;
        $empleado->calle = $request->calle;
        $empleado->numeroExt = $request->numeroExt;
        $empleado->numeroInt = $request->numeroInt;
        $empleado->colonia = $request->colonia;
        $empleado->codigoPostal = $request->codigoPostal;
        $empleado->delegacion = $request->delegacion;
        $empleado->ciudad = $request->ciudad;
        $empleado->referencias = $request->referencias;
        $empleado->descripcion = $request->descripcion;

        $empleado->save();

        $urlConfirmacion = env('URL_API_AUTENTICACION') .
            '/confirmar-registro/' .
            $request->id_user;

        if ($request->noEnvioCorreo) {
            $email = new BienvenidaController($request->correo_electronico);
        } else {
            $email = new CorreoController(
                $request->correo_electronico,
                $request->contrasena,
                $urlConfirmacion,
                $request->noEnvioCorreo
            );
        }

        Mail::to($request->correo_electronico)->send($email);

        return response()->json(
            Respuestas::respuesta200(
                'Empleado creado.',
                $empleado->id
            ),
            201
        );
    }

    public function crearEmpleadoLaboral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idEmpleado' => 'required|unique:mysql_dinamica.empleados_laborales',
            'area_id' => 'nullable',
            'puesto_id' => 'nullable',
            'sucursal_id' => 'nullable',
            'idCatalogoBanco' => 'nullable',
            'idJefeInmediato' => 'nullable',
            'salario' => 'nullable',
            'tipoContrato' => 'nullable',
            'clabeInterbancaria' => 'nullable',
            'periodoPago' => 'nullable',
            'horas_laborales' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()), 400);
        }

        EmpleadoLaborales::create($request->all());

        $namedb = $request->header('nombredb');
        $empleadosRespuesta = $this->consultarEmpleadoBD($namedb);

        return response()->json(Respuestas::respuesta200('Empleados encontrados.', $empleadosRespuesta));
    }

    public function traerFotografia($uuid, $extension, $nombre)
    {
        /**
         *  Método para borrar un documento
         */

        if (!$uuid) {
            return response()->json(Respuestas::respuesta400('No se tiene uuid'));
        }

        $ruta = '/empleados/' . $nombre . '/' . $uuid . '.' . $extension;
        return Storage::download(
            $ruta,
            $nombre .
                '.' .
                $extension
        );
    }

    public function consultarEmpleado($id, $uuidBD)
    {
        $empleado = EmpleadoLaborales::where('idEmpleado', $id)
            ->leftJoin('areas', 'empleados_laborales.area_id', '=', 'areas.id')
            ->leftJoin('puestos', 'empleados_laborales.puesto_id', '=', 'puestos.id')
            ->leftJoin('sucursales', 'empleados_laborales.sucursal_id', '=', 'sucursales.id')
            ->leftJoin('empleados', 'empleados_laborales.idEmpleado', '=', 'empleados.id')
            ->leftJoin('empleados AS jefe', 'empleados_laborales.idJefeInmediato', '=', 'jefe.id')
            ->leftJoin('catalogo_bancos', 'empleados_laborales.idCatalogoBanco', '=', 'catalogo_bancos.id')
            ->select(
                'empleados_laborales.*',
                'empleados.*',
                'jefe.nombres AS nombreJefeInmediato',
                'jefe.apellido_paterno AS apellidoPaternoJefeInmediato',
                'jefe.apellido_materno AS apellidoMaternoJefeInmediato',
                'jefe.nombres AS nombreJefeInmediato',
                'areas.area AS nombreArea',
                'puestos.puesto AS nombrePuesto',
                'catalogo_bancos.nombre AS nombreBancoClabe',
                'sucursales.nombreSucursal'
            )
            ->get()[0];

        if (!isset($empleado)) {
            return response()->json(Respuestas::respuesta404('Empleado no encontrado'));
        }


        if (isset($empleado['imagen']) && isset($empleado['extension'])) {
            $ruta = 'empleados/' . $uuidBD . '/' . $empleado->imagen . '.' . $empleado->extension;
            $base64 = Storage::get($ruta);
            $empleado['archivoImagen'] = base64_encode($base64);
            $empleado['roles'] = EmpleadoRoles::where('idEmpleado', $empleado['idEmpleado'])
                ->join('roles', 'roles.id', '=', 'empleados_roles.idRol')
                ->select('empleados_roles.*', 'roles.nombre AS nombreRol')
                ->get();
        }

        return response()->json(Respuestas::respuesta200('Empleado encontrado.', $empleado));
    }

    public function actualizarInformacionGeneralEmpleado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'uuidBD' => 'nullable',
            'nombres' => 'nullable',
            'apellido_paterno' => 'nullable',
            'apellido_materno' => 'nullable',
            'fecha_nacimiento' => 'nullable',
            'genero' => 'nullable',
            'estado_civil' => 'nullable',
            'curp' => 'nullable',
            'rfc' => 'nullable',
            'nss' => 'nullable',
            'telefono' => 'nullable',
            'correo_electronico' => 'nullable',
            'imagen' => 'nullable',
            'nombreImagen' => 'nullable',
            'calle' => 'nullable',
            'numeroExt' => 'nullable',
            'numeroInt' => 'nullable',
            'colonia' => 'nullable',
            'codigoPostal' => 'nullable',
            'delegacion' => 'nullable',
            'ciudad' => 'nullable',
            'referencias' => 'nullable',
            'baja' => 'nullable',
            'descripcion' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()));
        }

        $datosActualizados = [
            'id' => $request->id,
            'nombres' => $request->nombres,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
            'estado_civil' => $request->estado_civil,
            'curp' => $request->curp,
            'rfc' => $request->rfc,
            'nss' => $request->nss,
            'telefono' => $request->telefono,
            'correo_electronico' => $request->correo_electronico,
            'calle' => $request->calle,
            'numeroExt' => $request->numeroExt,
            'numeroInt' => $request->numeroInt,
            'colonia' => $request->colonia,
            'codigoPostal' => $request->codigoPostal,
            'delegacion' => $request->delegacion,
            'ciudad' => $request->ciudad,
            'baja' => $request->baja,
            'referencias' => $request->referencias,
            'descripcion' => $request->descripcion,
        ];

        if ($request->hasFile('imagen') && isset($request->uuidBD)) {
            $archivo = $request->file('imagen');
            $UUID = Str::orderedUuid();
            $extension = $archivo->getClientOriginalExtension();
            $datosActualizados['imagen'] = $UUID;
            $datosActualizados['extension'] = $extension;

            if (isset($request->nombreImagen)) {
                $rutaArchivo = $request->uuidBD . '/' .
                    $request->nombreImagen . '.' .
                    $request->extension;

                if (Storage::disk('empleados')->exists($rutaArchivo))
                    Storage::disk('empleados')->delete($rutaArchivo);
            }

            $archivo->storeAs(
                "/" . $request->uuidBD,
                $UUID . '.' . $extension,
                'empleados'
            );
        }

        $datosActualizados = array_filter($datosActualizados);

        if ($request->baja == 0) {
            $datosActualizados['baja'] = 0;
            $fechaActual = date('Y-m-d H:i:s');
            EmpleadoLaborales::where('idEmpleado', $request->id)
                ->update([
                    'fecha_reingreso' => $fechaActual
                ]);
        }

        if ($request->baja) {
            $fechaActual = date('Y-m-d H:i:s');
            EmpleadoLaborales::where('idEmpleado', $request->id)
                ->update([
                    'fechaBaja' => $fechaActual
                ]);
        }


        Empleado::where('id', $request->input('id'))
            ->update($datosActualizados);

        $empleados = $this->consultarEmpleadoBD($request->header('nombredb'));

        return response()->json(Respuestas::respuesta200('Empleado actualizado.', $empleados));
    }

    public function actualizarInformacionLaboralEmpleado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idEmpleado' => 'required',
            'area_id' => 'nullable',
            'puesto_id' => 'nullable',
            'sucursal_id' => 'nullable',
            'idCatalogoBanco' => 'nullable',
            'idJefeInmediato' => 'nullable',
            'salario' => 'nullable',
            'tipoContrato' => 'nullable',
            'clabeInterbancaria' => 'nullable',
            'periodoPago' => 'nullable',
            'fechaBaja' => 'nullable',
            'fecha_reingreso' => 'nullable',
            'horas_laborales' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(
                Respuestas::respuesta400($validator->errors())
            );
        }

        $datosActualizados = [
            'area_id' => $request->area_id,
            'puesto_id' => $request->puesto_id,
            'sucursal_id' => $request->sucursal_id,
            'idCatalogoBanco' => $request->idCatalogoBanco,
            'idJefeInmediato' => $request->idJefeInmediato,
            'salario' => $request->salario,
            'tipoContrato' => $request->tipoContrato,
            'clabeInterbancaria' => $request->clabeInterbancaria,
            'periodoPago' => $request->periodoPago,
            'fechaBaja' => $request->fechaBaja,
            'fecha_reingreso' => $request->fecha_reingreso,
            'horas_laborales' => $request->horas_laborales,
        ];

        $datosActualizados = array_filter($datosActualizados);

        EmpleadoLaborales::where('idEmpleado', $request->input('idEmpleado'))
            ->update($datosActualizados);

        $empleados = $this->consultarEmpleadoBD($request->header('nombredb'));

        return response()->json(
            Respuestas::respuesta200('Información Laboral del Empleado actualizado.', $empleados)
        );
    }

    public function eliminarEmpleado($id, Request $request)
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json(Respuestas::respuesta404('Empleado no encontrado'));
        }

        $empleado->delete();

        $empleados = $this->consultarEmpleadoBD($request->header('nombredb'));

        return response()->json(Respuestas::respuesta200('Empleado eliminado', $empleados), 201);
    }

    public function traerArchivo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'uuid' => 'string|required',
            'extension' => 'string|required',
            'area' => 'string|required',
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()));
        }

        $UUID = $request->input('uuid');
        $extension = $request->input('extension');
        $area = $request->input('area');

        return Storage::disk('empleados')->get($area . '/' . $UUID . "." . $extension);
    }

    public function descargarArchivo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'string|required',
            'extension' => 'string|required',
            'area' => 'string|required',
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()));
        }

        $UUID = $request->input('uuid');
        $extension = $request->input('extension');
        $area = $request->input('area');

        return Storage::disk('empleados')->download($area . '/' . $UUID . "." . $extension);
    }

    public function traerDocumentosArea($area)
    {
        /**
         *  Método para consultaer todos los documentos de una área
         */

        if (!$area) {
            return response()->json(Respuestas::respuesta400('No se tiene el área a buscar.'));
        }

        $documentos = DocEmpleado::where('area', $area)->where('activo', true)->get();

        if (count($documentos) < 1) {
            return response()->json(Respuestas::respuesta400('El área no se encontro.'));
        }

        return response()->json(
            Respuestas::respuesta200(
                'Consulta exitosa.',
                $documentos
            )
        );
    }

    public function actualizarPlantillaDocumentoEmpleado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'idEmpleado' => 'required',
            'uuid' => 'nullable',
            'file0' => 'nullable',
            'extension' => 'nullable',
            'activo' => 'nullable',
            'estatus' => 'nullable',
            'comentarios' => 'nullable'
        ]);

        $extensionNueva = '';
        $nombreBase = $request->header('nombredb');

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()), 400);
        }

        if (
            //CASO1: Se actualiza el documento y  el archivo
            $request->has('file0') &&
            $request->has('extension') &&
            $request->has('uuid')
        ) {


            Storage::delete(
                'documentos-empleados/' .
                    $nombreBase . '/' .
                    $request->idEmpleado . '/' .
                    $request->uuid . '.' .
                    $request->extension
            );

            $archivo = $request->file('file0');
            $extensionNueva = $archivo->getClientOriginalExtension();
            $this->UUID = Str::orderedUuid();

            $archivo->storeAs(
                "/" . $nombreBase . "/" . $request->idEmpleado,
                $this->UUID . '.' . $extensionNueva,
                'documentosEmpleados'
            );
        } elseif (
            $request->has('file0')
        ) {
            // CASO0: Se actualiza el documento y se crea el archivo

            $archivo = $request->file('file0');
            $extensionNueva = $archivo->getClientOriginalExtension();

            $this->UUID = Str::orderedUuid();

            $archivo->storeAs(
                "/" . $nombreBase . "/" . $request->idEmpleado,
                $this->UUID . '.' . $extensionNueva,
                'documentosEmpleados'
            );
        } else {
            // CASO 3: Se actualiza lo demás
            $this->UUID = $request->uuid;
        }

        $datosActualizado = [
            'uuid' => $this->UUID,
            'extension' => $extensionNueva,
            'estatus' => $request->estatus,
            'comentarios' => $request->comentarios,
        ];

        $datosActualizado = array_filter($datosActualizado);


        if ($request->has('activo')) {
            $datosActualizado = [
                'activo' => false,
            ];
        }

        DocEmpleado::where('id', $request->input('id'))
            ->update($datosActualizado);

        return $this->consultarPlantillasDocumentosEmpleado($request->idEmpleado);
    }

    public function consultarPlantillasDocumentosEmpleado($idEmpleado)
    {
        $documentos = DocEmpleado::where('idEmpleado', $idEmpleado)
            ->join(
                'plantilla_documentos',
                'plantilla_documentos.id',
                '=',
                'documentos_empleado.idPlantillaDocumento'
            )
            ->select('documentos_empleado.*', 'plantilla_documentos.nombreDocumento', 'plantilla_documentos.especificaciones')
            ->get();

        return response()->json(
            Respuestas::respuesta200(
                'Consulta exitosa.',
                $documentos
            )
        );
    }

    public function agregarPlantillaDocumentoEmpleado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idEmpleado' => 'int|required',
            'idPlantillaDocumento' => 'required',
            'estatus' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()), 400);
        }

        DocEmpleado::create($request->all());

        $documentosEmpleado = DocEmpleado::where('idEmpleado', $request->idEmpleado)
            ->join('plantilla_documentos', 'plantilla_documentos.id', '=', 'documentos_empleado.idPlantillaDocumento')
            ->select('documentos_empleado.*', 'plantilla_documentos.nombreDocumento', 'plantilla_documentos.especificaciones')
            ->get();

        return response()->json(
            Respuestas::respuesta200(
                'Documento asignado al empleado.',
                $documentosEmpleado
            )
        );
    }

    public function desasignarPlantillaDocumento(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idEmpleado' => 'int|required',
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()), 400);
        }


        $documento = DocEmpleado::find($request->id);

        if (!$documento) {
            return response()->json(Respuestas::respuesta404('Documento no encontrado'));
        }

        $documento->delete();

        return $this->consultarPlantillasDocumentosEmpleado($request->idEmpleado);
    }

    public function descargarDocumento(Request $request, $uuid, $extension, $idEmpleado)
    {
        if (!$uuid) {
            return response()->json(Respuestas::respuesta400('No se tiene uuid'));
        }

        $ruta = '/documentos-empleados/' .
            $request->header('nombredb') . '/' .
            $idEmpleado . '/' .
            $uuid . '.' . $extension;

        return Storage::download(
            $ruta,
            $uuid . '.' .
                $extension
        );
    }

    public function guardarAsistencia(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id_emp' => 'int|required',
            'fecha' => 'nullable',
            'estatus' => 'nullable',
            'hora_entrada' => 'nullable',
            'hora_salida' => 'nullable',
            'horas' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()));
        }

        $documentoRespuesta = RegistroAsistencias::create($request->all());

        $documentoRespuesta['fecha'] = str_replace("\\", "", $documentoRespuesta['fecha']);

        return response()->json(Respuestas::respuesta200('Archivo guardado.', $documentoRespuesta));
    }

    public function actualizarAsistencia(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'int|required',
            'id_emp' => 'nullable',
            'fecha' => 'nullable',
            'estatus' => 'nullable',
            'hora_entrada' => 'nullable',
            'hora_salida' => 'nullable',
            'horas' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()));
        }

        $datosActualizados = [
            'id' => $request->id,
            'hora_salida' => $request->hora_salida,
            'horas' => $request->horas
        ];

        $datosActualizados = array_filter($datosActualizados);


        RegistroAsistencias::where('id', $request->input('id'))
            ->update($datosActualizados);

        $registrosRespuesta = RegistroAsistencias::find($request->input('id'));

        return response()->json(Respuestas::respuesta200('Registro Actualizado.', $registrosRespuesta, 201));
    }

    public function traerRegistroAsistencia($id_emp, $fecha)
    {
        if (!isset($id_mpl) && !isset($fecha)) {
            return response()->json(
                Respuestas::respuesta400('No se enviarón datos.'),
                400
            );
        }
        $dia = substr($fecha, 0, 2);
        $mes = substr($fecha, 2, 2);
        $anio = substr($fecha, 4, 4);

        $fecha_formateada = $dia . '/' . $mes . '/' . $anio;

        $registrosRespuesta = RegistroAsistencias::where([
            'id_emp' => $id_emp,
            'fecha' => $fecha_formateada,
        ])->get();

        if (count($registrosRespuesta) < 1) {
            return response()->json(Respuestas::respuesta400('El registro no se encontro.'));
        }

        return response()->json(
            Respuestas::respuesta200(
                'Consulta exitosa.',
                $registrosRespuesta[0]
            )
        );
    }

    private function consultarEmpleados()
    {
        $empleados = EmpleadoLaborales::leftJoin('areas', 'empleados_laborales.area_id', '=', 'areas.id')
            ->leftJoin('puestos', 'empleados_laborales.puesto_id', '=', 'puestos.id')
            ->leftJoin('sucursales', 'empleados_laborales.sucursal_id', '=', 'sucursales.id')
            ->leftJoin('empleados', 'empleados_laborales.idEmpleado', '=', 'empleados.id')
            ->leftJoin('empleados AS jefe', 'empleados_laborales.idJefeInmediato', '=', 'jefe.id')
            ->leftJoin('catalogo_bancos', 'empleados_laborales.idCatalogoBanco', '=', 'catalogo_bancos.id')
            ->select(
                'empleados_laborales.*',
                'empleados.*',
                'jefe.nombres AS nombreJefeInmediato',
                'jefe.apellido_paterno AS apellidoPaternoJefeInmediato',
                'jefe.apellido_materno AS apellidoMaternoJefeInmediato',
                'jefe.nombres AS nombreJefeInmediato',
                'areas.area AS nombreArea',
                'puestos.puesto AS nombrePuesto',
                'catalogo_bancos.nombre AS nombreBancoClabe',
                'sucursales.nombreSucursal'
            )
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno')
            ->orderBy('nombres')
            ->get();

        $empleadosRespuesta = [];

        if (count($empleados) > 0) {
            foreach ($empleados as $empleado) {
                if (isset($empleado['imagen']) && isset($empleado['extension'])) {
                    $ruta = 'empleados/' . $empleado->imagen . '.' . $empleado->extension;
                    $base64 = Storage::get($ruta);
                    $empleado['archivoImagen'] = base64_encode($base64);
                    $empleado['roles'] = EmpleadoRoles::where('idEmpleado', $empleado['idEmpleado'])
                        ->join('roles', 'roles.id', '=', 'empleados_roles.idRol')
                        ->select('empleados_roles.*', 'roles.nombre AS nombreRol')
                        ->get();
                }
                array_push($empleadosRespuesta, $empleado);
            }
        }

        return $empleadosRespuesta;
    }
}
