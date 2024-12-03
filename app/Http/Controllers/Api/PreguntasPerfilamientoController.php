<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PerfilacionPregunta;
use App\Models\PerfilacionRespuesta;
use App\Models\Perfilacion;
use App\Models\Pais;
use App\Models\Plan;

class PreguntasPerfilamientoController extends Controller
{
    //Primer pregunta del perfilamiento
    public function inicio(Request $request){
        //Se deben enviar dos preguntas
        $pregunta = PerfilacionPregunta::where('id', '=', 1)->first();
        $respuestas = PerfilacionRespuesta::where('perfilacion_pregunta_id', 1)
            ->get()
            ->makeHidden(['created_at', 'updated_at'])
            ->toArray();
        return response()->json(
            [
                //Donde estás ubicado
                'pregunta' => [
                    'id_pregunta' => $pregunta->id,
                    'texto' => $pregunta->texto,
                    'tipo_pregunta' => $pregunta->tipo_pregunta,
                    'respuestas' => $respuestas 
                ],
            ],
            200
        );
    }

    //Determina la siguiente pregunta, dependiendo de la pregunta anterior
    public function siguiente_pregunta(Request $request){
        // Valida que tenga el campo seleccionado antes de enviar.
        $validated = $request->validate([
            'pregunta_perfilacion_id' => 'required|exists:perfilacion_preguntas,id'
        ]);
        $pregunta = PerfilacionPregunta::where('id', '=', $validated['pregunta_perfilacion_id'])->first();
        $respuestas = PerfilacionRespuesta::where('perfilacion_pregunta_id', $validated['pregunta_perfilacion_id'])
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

    //Guarda las respustas de las preguntas
    public function store(Request $request){
        // Validar que el JSON de respuestas esté presente y en el formato correcto
        $request->validate([
            'respuestas' => 'required|array',
            'respuestas.*.pregunta_id' => 'required|integer',
            'respuestas.*.tipo_pregunta' => 'required|string',
            'respuestas.*.respuesta_sel_multiple' => 'required',
        ]);

        //Recomendar plan, según al respuesta
        $plan_recomendado = recomendar_plan($request->input('respuestas'));
        // Crear el registro en la tabla perfilacion
        $perfilacion = new Perfilacion([
            'user_id' => auth()->id(), // Suponiendo que estás almacenando el ID del usuario autenticado
            'respuestas' => json_encode($request->input('respuestas')), // Almacenar las respuestas en formato JSON
        ]);
        // Guardar en la base de datos
        $perfilacion->save();
        // Retornar una respuesta exitosa
        return response()->json([
            'message' => 'Respuestas guardadas exitosamente.',
            'data' => $perfilacion,
            'plan_recomendado' => $plan_recomendado
        ], 201);
    }

    //Mostrar todos los planes disponibles
    public function planes_individuales(){
        $planes = Plan::where('tipo', '=', 'individual')->get()->makeHidden(['created_at', 'updated_at','tipo'])->toArray();
        // Retornar una respuesta exitosa
        return response()->json([
            'message' => 'Respuestas guardadas exitosamente.',
            'planes' => $planes
        ], 201);
    }
}

//Recomendación de planes individuales dentro de la aplicación
function recomendar_plan($respuestas){
    //--------
    //NOTA - FALTA VALIDAR SI EL USUARIO ESTA EN ESPAÑA, EN CASO DE QUE SÍ, CAMBIAR EL PREMOUM POR EL FLEX 
    //-------
    $planes = Plan::where('tipo', '=', 'individual')->get()->makeHidden(['created_at', 'updated_at','tipo'])->toArray();
    $plan = null;

    $gratis = $planes[0];
    $pro = $planes[1];
    $anytime = $planes[2];
    $full = $planes[3];
    $flex = $planes[4];

    //INDIVIDUALES
    // ID = 1 - MIA GRATIS - Plan gratuito___Mia
    // 2 - Anytime___Anytime
    // 3 - Flex --- Flex
    // 4 - Full - Plus___Full
    // 5 - Pro - Premium___Pro
    
    //Sigue la recomendación de planes individuales
    for ($i=0; $i <count($respuestas) ; $i++) { 
        if($respuestas[$i]['respuesta_sel_multiple'] == 1){  //Recursos de autoayuda y orientación inicial
            $plan = $gratis;
        } else if($respuestas[$i]['respuesta_sel_multiple'] == 4 ){ //Prefiero el apoyo autodirigido
            $plan = $anytime;
        } else if($respuestas[$i]['respuesta_sel_multiple'] == 5){  //Chat 
            $plan = $pro;
        } else if($respuestas[$i]['respuesta_sel_multiple'] == 7){  //Accesibilidad y conveniencia
            $plan = $full;
        } else if($respuestas[$i]['respuesta_sel_multiple'] == 8){  //Profesionales con años de experiencia y estudios
            $plan = $pro;
        }else{
            $plan = $gratis;
        }
    }
    return $plan;
}
