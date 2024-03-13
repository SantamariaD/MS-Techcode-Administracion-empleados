<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Respuestas\Respuestas;
use App\Models\PlantillaDocumentos;
use Illuminate\Support\Facades\Validator;

class PlantillaDocumentosController extends Controller
{

    public function consultarPlantillas()
    {
        $plantillas = PlantillaDocumentos::where('activo', true)->get();

        return response()->json(
            Respuestas::respuesta200(
                'Consulta exitosa.',
                $plantillas
            )
        );
    }


    public function crearPlantilla(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombreDocumento' => 'required',
            'especificaciones' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(Respuestas::respuesta400($validator->errors()), 400);
        }

        PlantillaDocumentos::create($request->all());

        $plantillas = PlantillaDocumentos::where('activo', true)->get();

        return response()->json(Respuestas::respuesta200('Plantilla creada.', $plantillas), 201);
    }


    public function atualizarPlantilla(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'nombreDocumento' => 'nullable',
            'especificaciones' => 'nullable',
            'activo' => 'nullable',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        };

        $datosActualizados = [
            'nombreDocumento' => $request->nombreDocumento,
            'especificaciones' => $request->especificaciones,
        ];

        $datosActualizados = array_filter($datosActualizados);

        if (isset($request->activo)) {
            $datosActualizados = [
                'activo' => false,
            ];
        }

        PlantillaDocumentos::where('id', $request->id)
            ->update($datosActualizados);

        $plantillas = PlantillaDocumentos::where('activo', 1)->get();

        return response()->json(
            Respuestas::respuesta200(
                'Plantilla actualizada.',
                $plantillas
            ),
        );
    }
}
