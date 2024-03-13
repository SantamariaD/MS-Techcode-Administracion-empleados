<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Horario;
use Carbon\Carbon;
use App\Respuestas\Respuestas;
use Illuminate\Support\Facades\Validator;

class HorarioController extends Controller
{
    public function crearHorario(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idEmpleado' => 'required',
            'idHorGeneral' => 'required',
            'fecha' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()));
        }

        $horarios = Horario::create($request->all());

        return response()->json(Respuestas::respuesta200('Horario Asignado al empleado.', $horarios), 201);
    }

    public function crearVarios(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'horarios' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors(), 400));
        }

        $horarios = $request->input('horarios');

        foreach ($horarios as $horario) {

            Horario::create($horario);
        }

        $todosHorarios = Horario::orderBy('id')
            ->get();

        return response()->json(Respuestas::respuesta200('Los horarios se guardaron de manera correcta.', $todosHorarios), 201);
    }

    public function actualizarHorario(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'idEmpleado' => 'required',
            'idHorGeneral' => 'required',
            'fecha' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()));
        }

        $datosActualizados = [
            'id' => $request->id,
            'idEmpleado' => $request->idEmpleado,
            'idHorGeneral' => $request->idHorGeneral,
            'fecha' => $request->fecha,

        ];

        Horario::where('id', $request->input('id'))
            ->update($datosActualizados);

        $horarios = Horario::orderBy('id')
            ->get();




        return response()->json(Respuestas::respuesta200('Horario actualizado.', $horarios), 201);
    }

    public function actualizarVarios(request $request)
    {
        $validator = Validator::make($request->all(), [
            'horarios' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors(), 400));
        }

        $horarios = $request->input('horarios');

        foreach ($horarios as $horario) {
            $datosActualizados = [
                'id' => $horario['id'],
                'idEmpleado' => $horario['idEmpleado'],
                'idHorGeneral' => $horario['idHorGeneral'],
                'fecha' => $horario['fecha'],
            ];
            Horario::where('id', $horario['id'])
                ->update($datosActualizados);
        }

        $todosHorarios = Horario::orderBy('id')
            ->get();

        return response()->json(Respuestas::respuesta200('Los horarios se actualizaron de manera correcta.', $todosHorarios), 201);
    }

    public function consultarTodosHorarios()
    {
        $horarios = Horario::join('horarios_generales', 'horarios.idHorGeneral', '=', 'horarios_generales.id')
            ->select('horarios.*',  'horarios_generales.id AS idHorGeneral', 'horarios_generales.nombre AS nombre', 'horarios_generales.hora_entrada AS hora_entrada', 'horarios_generales.hora_salida AS hora_salida', 'horarios_generales.horas AS horas')
            ->orderBy('id')
            ->get();
        return response()->json(Respuestas::respuesta200('Horarios encontrados.', $horarios));
    }



    public function consultarHorariosById($idEmpleado)
    {
        $registros = Horario::join('horarios_generales', 'horarios.idHorGeneral', '=', 'horarios_generales.id')
            ->select('horarios.*', 'horarios_generales.id AS idHorGeneral', 'horarios_generales.nombre AS nombre', 'horarios_generales.hora_entrada AS hora_entrada', 'horarios_generales.hora_salida AS hora_salida', 'horarios_generales.horas AS horas')
            ->where('horarios.idEmpleado', $idEmpleado)
            ->orderBy('horarios.id') // Puedes cambiar 'horarios_generales.id' por la columna que desees ordenar
            ->get();

        return response()->json(Respuestas::respuesta200('Registros de horarios.', $registros));
    }



    public function consultarHorarioByFecha($fecha, $idEmpleado)
    {

        $registros = Horario::join('horarios_generales', 'horarios.idHorGeneral', '=', 'horarios_generales.id')
            ->select('horarios.*', 'horarios_generales.id AS idHorGeneral', 'horarios_generales.nombre AS nombre', 'horarios_generales.hora_entrada AS hora_entrada', 'horarios_generales.hora_salida AS hora_salida', 'horarios_generales.horas AS horas')
            ->where('horarios.idEmpleado', $idEmpleado)
            ->where('fecha', $fecha)
            ->orderBy('horarios_generales.id') // Puedes cambiar 'horarios_generales.id' por la columna que desees ordenar
            ->get();

        if (count($registros) < 1) {
            return response()->json(Respuestas::respuesta200('No se encontrarón resultados', $registros));
        }

        return response()->json(Respuestas::respuesta200('registros de horarios.', $registros));
    }

    public function eliminarHorario($id)
    {
        $horario = Horario::find($id);

        if (!$horario) {
            return response()->json(Respuestas::respuesta404('horario no encontrado'));
        }

        $horario->delete();

        $horarios = Horario::orderBy('id')
            ->get();

        return response()->json(Respuestas::respuesta200NoResultados('horario eliminado.', $horarios));
    }
}
