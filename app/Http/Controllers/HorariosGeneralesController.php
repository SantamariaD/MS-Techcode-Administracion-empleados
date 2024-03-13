<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HorariosGenerales;
use Carbon\Carbon;
use App\Respuestas\Respuestas;
use Illuminate\Support\Facades\Validator;

class HorariosGeneralesController extends Controller
{
    public function crearHorario(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'nullable',
            'horas' => 'required',
            'hora_entrada' =>'required',
            'hora_salida' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()));
        }

        $horarios = HorariosGenerales::create($request->all());

        return response()->json(Respuestas::respuesta200('Horario Creado.', $horarios), 201);
    }

    public function actualizarHorario(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'nombre' => 'nullable',
            'horas' => 'required',
            'hora_entrada' =>'required',
            'hora_salida' => 'required',
            'baja' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()));
        }

        // $baja = $request->input('baja') ?? 0;

        $datosActualizados = [
            'id' => $request -> id,
            'nombre' => $request->nombre,
            'horas' => $request->horas,
            'hora_entrada' => $request->hora_entrada,
            'baja' => $request->baja,
            'hora_salida' => $request->hora_salida,
        ];

        HorariosGenerales::where('id', $request->input('id'))
        ->update($datosActualizados);

        $horarios = HorariosGenerales::orderBy('id')
        ->get();
 



    return response()->json(Respuestas::respuesta200('Horario actualizado.',$horarios),201);

    }

    public function consultarTodosHorarios()
    {
        $horarios = HorariosGenerales::orderBy('id') ->get();
        return response()->json(Respuestas::respuesta200('Horarios encontrados.', $horarios));
    }



    public function consultarHorariosById($id){

        $registros = HorariosGenerales::where('id', $id)
        ->get();

        if (!$registros) {
            return response()->json(Respuestas::respuesta400($validator->errors()));
        }

        if (count($registros) < 1) {
            return response()->json(Respuestas::respuesta400('No se encontrarón resultados'), 400);
        }

        return response()->json(Respuestas::respuesta200('Horario encontrado.', $registros));

    }


    public function eliminarHorario($id)
    {
        $horario = HorariosGenerales::find($id);

        if (!$horario) {
            return response()->json(Respuestas::respuesta404('horario no encontrado'));
        }

        $horario->delete();

        $horarios= HorariosGenerales::orderBy('id')
        ->get();

        return response()->json(Respuestas::respuesta200NoResultados('horario eliminado.', $horarios));
    }
}
