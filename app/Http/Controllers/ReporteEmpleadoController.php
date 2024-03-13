<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puestos;
use App\Models\ReporteEmpleado;
use App\Respuestas\Respuestas;
use Illuminate\Support\Facades\Validator;

class ReporteEmpleadoController extends Controller
{

    public function crearReporte(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'id_empleado' => 'required',
            'fecha' => 'required',
            'id_horario' => 'nullable',
            'id_registro' => 'nullable',
            'sueldo_dia' => 'required',
            'hora_extra' => 'nullable',
            'comisiones' => 'nullable',
            'otros_ganancia' => 'nullable',
            'total_ganancia' => 'required',
            'hora_descontada' => 'nullable',
            'adelantos' => 'nullable',
            'otros_descuento' => 'nullable',
            'total_descuento' => 'required',
            'comentarios' => 'nullable',
            'total' => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()));
        }



        $reportes = ReporteEmpleado::create($request->all());

        return response()->json(Respuestas::respuesta200('El reporte se ha creado de manera correcta.', $reportes), 201);
    }

    public function actualizarReporte(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'id_empleado' => 'required',
            'fecha' => 'required',
            'id_horario' => 'nullable',
            'id_registro' => 'nullable',
            'sueldo_dia' => 'required',
            'hora_extra' => 'nullable',
            'comisiones' => 'nullable',
            'otros_ganancia' => 'nullable',
            'total_ganancia' => 'required',
            'hora_descontada' => 'nullable',
            'adelantos' => 'nullable',
            'otros_descuento' => 'nullable',
            'total_descuento' => 'required',
            'comentarios' => 'nullable',
            'total' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()));
        }

        $datosActualizados = [
            'id' => $request->id,
            'id_empleado' => $request->id_empleado,
            'fecha' => $request->fecha,
            'id_horario' => $request->id_horario,
            'id_registro' => $request->id_registro,
            'sueldo_dia' => $request->sueldo_dia,
            'sueldo_dia' => $request->sueldo_dia ?? '0.00',
            'hora_extra' => $request->hora_extra ?? '0.00',
            'comisiones' => $request->comisiones ?? '0.00',
            'otros_ganancia' => $request->otros_ganancia ?? '0.00',
            'total_ganancia' => $request->total_ganancia ?? '0.00',
            'hora_descontada' => $request->hora_descontada ?? '0.00',
            'adelantos' => $request->adelantos ?? '0.00',
            'otros_descuento' => $request->otros_descuento ?? '0.00',
            'total_descuento' => $request->total_descuento ?? '0.00',
            'comentarios' => $request->comentarios,
            'total' => $request->total,
        ];

        ReporteEmpleado::where('id', $request->input('id'))
            ->update($datosActualizados);

        $reportes = ReporteEmpleado::orderBy('fecha')
            ->get();




        return response()->json(Respuestas::respuesta200('Reporte actualizado.', $reportes), 201);
    }

    public function consultarTodosReportes()
    {
        $reportes = ReporteEmpleado::orderBy('fecha')->get();
        return response()->json(Respuestas::respuesta200('Reportes encontrados.', $reportes));
    }

    public function consultarMuchosByFecha(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'arregloReportes' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors(), 400));
        }

        $arregloReportes = $request->input('arregloReportes');
        $todosReportes = [];

        foreach ($arregloReportes as $reporte) {

            $reporteEncontrado = ReporteEmpleado::where('fecha', $reporte['fecha'])
                ->where('id_empleado', $reporte['id'])
                ->get();

            array_push($todosReportes, $reporteEncontrado);
        }

        return response()->json(Respuestas::respuesta200('Reportes del periodo seleccionado.', $todosReportes), 201);
    }

    public function consultarVariosIdNomina(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idsNomina' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors(), 400));
        }

        $arregloReportes = $request->input('idsNomina');
        $todosReportes = [];

        foreach ($arregloReportes as $reporte) {

            $reportesEncontrados = ReporteEmpleado::where('id_nomina', $reporte)
                ->get();

            foreach ($reportesEncontrados as $reporteEncontrado) {
                array_push($todosReportes, $reporteEncontrado);
            }
        }

        return response()->json(Respuestas::respuesta200('Reportes de empleados en este periodo.', $todosReportes), 201);
    }

    public function consultarReportesByIdNomina($idNomina)
    {

        $registros = ReporteEmpleado::where('id_nomina', $idNomina)
            ->get();

        if (!$registros) {
            return response()->json(Respuestas::respuesta400($validator->errors()));
        }

        if (count($registros) < 1) {
            return response()->json(Respuestas::respuesta400('No se encontrarón resultados'), 400);
        }

        return response()->json(Respuestas::respuesta200('registros de reportes.', $registros));
    }



    public function consultarReportesById($idEmpleado)
    {
        $registros = ReporteEmpleado::where('id_empleado', $idEmpleado)
            ->get();

        return response()->json(Respuestas::respuesta200('registros de reportes.', $registros));
    }


    public function consultarReporteByFecha($fecha, $idEmpleado)
    {
        $registros = ReporteEmpleado::where('id_empleado', $idEmpleado)
            ->where('fecha', $fecha)
            ->get();

        if (count($registros) < 1) {
            return response()->json(Respuestas::respuesta200('No se encontrarón resultados', $registros));
        }

        return response()->json(Respuestas::respuesta200('registros de reportes.', $registros));
    }

    public function eliminarReporte($id)
    {
        $reporte = ReporteEmpleado::find($id);

        if (!$reporte) {
            return response()->json(Respuestas::respuesta404('reporte no encontrado'));
        }

        $reporte->delete();

        $reporte = ReporteEmpleado::orderBy('id')
            ->get();

        return response()->json(Respuestas::respuesta200NoResultados('reporte eliminado.', $reporte));
    }
}
