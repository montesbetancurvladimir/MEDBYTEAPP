<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OnboardingPregunta;
use App\Models\OnboardingRespuesta;
use App\Models\Onboarding;

class PreguntasOnboardingController extends Controller
{
    //Primer pregunta del onboarding
    public function inicio( Request $request){
        $pregunta = OnboardingPregunta::where('id', '=', 1)->first();
        $respuestas = OnboardingRespuesta::where('onboarding_pregunta_id', 1)
            ->get()
            ->makeHidden(['created_at', 'updated_at'])
            ->toArray();

        return response()->json(
            [
                'id_pregunta' => $pregunta->id,
                'texto' => $pregunta->texto,
                'tipo_pregunta' => $pregunta->tipo_pregunta,
                'respuestas' => $respuestas
            ],
            200
        );
    }

    //Determina la siguiente pregunta, dependiendo de la pregunta anterior
    public function siguiente_pregunta(Request $request){
        // Valida que tenga el campo seleccionado antes de enviar.
        $validated = $request->validate([
            'pregunta_onboarding_id' => 'required|exists:onboarding_preguntas,id'
        ]);
        $pregunta = OnboardingPregunta::where('id', '=', $validated['pregunta_onboarding_id'])->first();
        $respuestas = OnboardingRespuesta::where('onboarding_pregunta_id', $validated['pregunta_onboarding_id'])
            ->get()
            ->makeHidden(['created_at', 'updated_at'])
            ->toArray();

        //La única pregunta abierta.
        if($validated['pregunta_onboarding_id'] == 3){
            return response()->json(
                [
                    'id_pregunta' => $pregunta->id,
                    'texto' => $pregunta->texto,
                    'tipo_pregunta' => $pregunta->tipo_pregunta,
                    'siguiente_onboarding_pregunta_id' => 4
                ],
                200
            );
        }else{
            return response()->json(
                [
                    'id_pregunta' => $pregunta->id,
                    'texto' => $pregunta->texto,
                    'tipo_pregunta' => $pregunta->tipo_pregunta,
                    'respuestas' => $respuestas
                ],
                200
            );
        }
    }

    //Guarda las respustas de las preguntas
    public function store(Request $request){
        // Validar que el JSON de respuestas esté presente y en el formato correcto
        $request->validate([
            'respuestas' => 'required|array',
            'respuestas.*.pregunta_id' => 'required|integer',
            'respuestas.*.tipo_pregunta' => 'required|string|in:multiple_choice,open,draw',
            'respuestas.*.respuesta_abierta' => 'nullable|string',
            'respuestas.*.respuesta_sel_multiple' => 'nullable|string',
        ]);
        // Crear el registro en la tabla onboardings
        $onboarding = new Onboarding([
            'user_id' => auth()->id(), // Suponiendo que estás almacenando el ID del usuario autenticado
            'respuestas' => json_encode($request->input('respuestas')), // Almacenar las respuestas en formato JSON
        ]);
        // Guardar en la base de datos
        $onboarding->save();
        // Retornar una respuesta exitosa
        return response()->json([
            'message' => 'Respuestas guardadas exitosamente.',
            'data' => $onboarding
        ], 201);
    }
}
