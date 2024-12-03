<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Option;
use App\Models\SurveyResponse;
use App\Models\OpenResponse;
use App\Models\Pais;
use App\Models\User;
use App\Models\Plan;
use App\Models\CountryResponse;

//Encriptador de la contraseña
use Illuminate\Support\Facades\Hash;
//Guardar las respuestas en la sesión
use Illuminate\Support\Facades\Session;

class SurveyController extends Controller
{
    //Controlador para la perfilación de usuarios dentro de la aplicación.
    //Primer pregunta.
    public function start(){
        $question = Question::first();
        $paises = Pais::all();
        return response()->json(['question' => $question, 'paises' => $paises]);
    }

    //Listado de preguntas de perfilación
    public function answer_cache(Request $request){
        // Variables para recomendar el plan, responde la última pregunta.
        $empresa_valor = null;
        $individual_valor = null;
        $eliminar_plan = false;

        // Verificar y asignar valor vacío si 'response' o 'selected_option' no están presentes en la solicitud
        $request->merge(['response' => $request->get('response', ''), 'selected_option' => $request->get('selected_option', '')]);

        // Valida que tenga el campo seleccionado antes de enviar.
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'response' => 'nullable|string',
            'selected_option' => 'nullable|string'
        ]);

        // Antes de guardar la pregunta, necesitamos validar si la pregunta (4) que es la de la edad, tenga más de 18 años.
        if ($validated['question_id'] == 4) {
            $validad_edad = validar_edad($validated['response']);
            if ($validad_edad == false) {
                return response()->json(['error' => 'Debe ser mayor de edad para hacer uso de nuestros servicios.'], 403);
            }
        }

        // Guardar la respuesta de la pregunta en la memoria cache
        session()->put('responses.' . $validated['question_id'], [
            'question_id' => $validated['question_id'],
            'response' => $validated['response'],
            'selected_option' => $validated['selected_option'],
        ]);

        // Crear la respuesta en función del tipo de pregunta (abierta o selección multiple)
        if ($request->question_id == 12) {
            $question = Question::where('id', '=', 6)->first();
        } else {
            if ($request->filled('selected_option')) {
                if ($validated['question_id'] == 1) {
                    $nextOption = Pais::find($validated['selected_option']);
                    $question = $nextOption->nextQuestion;
                } else {
                    $nextOption = Option::find($validated['selected_option']);
                    $question = $nextOption->nextQuestion;
                }
            } else {
                $question = Question::where('id', '>', $request->question_id)->first();
            }
        }

        // Valida si es la última pregunta para guardar los datos almacenados en la sesión.
        if ($request->question_id == 6) {
            $variables = session()->get('responses');
            $email = $request->response;
            $name = explode('@', $email)[0];
            $user = User::firstOrCreate(['email' => $email], ['name' => $name, 'password' => Hash::make('123456789')]);

            foreach ($variables as $response) {
                if ($response['selected_option']) {
                    if ($response['question_id'] == 1) {
                        CountryResponse::create([
                            'user_id' => $user->id,
                            'question_id' => $response['question_id'],
                            'pais_id' => $response['selected_option'],
                        ]);
                    } else {
                        SurveyResponse::create([
                            'user_id' => $user->id,
                            'question_id' => $response['question_id'],
                            'option_id' => $response['selected_option'],
                        ]);
                    }
                    $recommendedPlan = recomendar_plan($empresa_valor, $individual_valor, $response['selected_option']);
                } else if ($response['response']) {
                    OpenResponse::create([
                        'user_id' => $user->id,
                        'question_id' => $response['question_id'],
                        'response' => $response['response'],
                    ]);
                }
            }
            session()->forget('responses');
        }

        if ($request->question_id == 6) {
            if ($recommendedPlan['individual'] == null) {
                $PlanRecomendado = $recommendedPlan['empresa'];
                return response()->json(['PlanRecomendado' => $PlanRecomendado, 'tipo' => 'empresa']);
            } else {
                $planes = Plan::where('tipo', '=', 'individual')->get();
                $PlanRecomendado = $recommendedPlan['individual'];
                $bandera_espana = false;
                $eliminar_plan = eliminar_plan_extra($variables, $PlanRecomendado, $bandera_espana);
                if ($eliminar_plan['espana'] == true && $PlanRecomendado == 'Flex') {
                    $PlanRecomendado = 'Full';
                }
                $planes_actualizados = eliminar_plan_del_arreglo($planes, $eliminar_plan['plan_eliminar']);
                return response()->json(['PlanRecomendado' => $PlanRecomendado, 'planes_actualizados' => $planes_actualizados]);
            }
        } else {
            return $question
                ? response()->json(['question' => $question, 'tipo_pregunta' => $question->type])
                : response()->json(['message' => 'Survey complete.']);
        }
    }

    //Recomendar el plan
    public function complete(Request $request){
        $planes = Plan::where('tipo', '=', 'individual')->get();
        $planes_actualizados = eliminar_plan_del_arreglo($planes, 'Full');
        //Lista de planes y Plan Recomendado (nombre)
        return response()->json(['planes_actualizados' => $planes_actualizados, 'PlanRecomendado' => '']);
    }
    
}

// Según las respuestas del cliente, se le recomienda un plan.
function recomendar_plan($empresa, $individual, $respuesta){
    // INDIVIDUALES
    if ($respuesta == 9) {
        $individual = 'Mia';
    } else if ($respuesta == 13) {
        $individual = 'Anytime';
    } else if ($respuesta == 12) {
        $individual = 'Pro';
    } else if ($respuesta == 15) {
        $individual = 'Flex';
    } else if ($respuesta == 19) {
        $individual = 'Pro';
    }
    // EMPRESAS
    else if ($respuesta == 20) {
        $empresa = 'Care_EMPRESAS';
    } else if ($respuesta == 22) {
        $empresa = 'Anytime_EMPRESAS';
    } else if ($respuesta == 23) {
        $empresa = 'Full_EMPRESAS';
    } else if ($respuesta == 6) {
        $empresa = 'Care_EMPRESAS';
    }
    return ['empresa' => $empresa, 'individual' => $individual];
}

function validar_edad($respuesta){
    return $respuesta >= 18;
}

function eliminar_plan_extra($respuestas, $plan_recomendado, $bandera_espana){
    $plan_eliminar = '';
    foreach ($respuestas as $respuesta) {
        if ($respuesta['question_id'] == 1 && $respuesta['selected_option'] == 186) {
            $bandera_espana = true;
        }
    }
    if ($bandera_espana) {
        if ($plan_recomendado == 'Mia') {
            $plan_eliminar = 'Full';
        } else if ($plan_recomendado == 'Anytime') {
            $plan_eliminar = 'Pro';
        } else if ($plan_recomendado == 'Pro') {
            $plan_eliminar = 'Full';
        } else if ($plan_recomendado == 'Full') {
            $plan_eliminar = 'Mia';
        } else if ($plan_recomendado == 'Flex') {
            $plan_eliminar = 'Flex';
        }
    } else {
        $plan_eliminar = 'Full';
    }
    return ['plan_eliminar' => $plan_eliminar, 'espana' => $bandera_espana];
}

function eliminar_plan_del_arreglo($planes, $nombre){
    $planes_filtrados = $planes->filter(function ($plan) use ($nombre) {
        return $plan->nombre !== $nombre;
    });
    return $planes_filtrados->values();
}
