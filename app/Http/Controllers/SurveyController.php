<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Option;
use App\Models\SurveyResponse;
use App\Models\OpenResponse;
use App\Models\Pais;
use App\Models\User;
use App\Models\Plan;
use App\Models\CountryResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionCorreo;

//Encriptador de la contraseña
use Illuminate\Support\Facades\Hash;
//Guardar las respuestas en la sesión
use Illuminate\Support\Facades\Session;

class SurveyController extends Controller
{

    public function start(){
        $question = Question::first();
        $paises = Pais::all();
        return view('survey.question', compact('question','paises'));
    }

    public function answer_cache(Request $request){

        //Variables para recomendar el plan, responde la última pregunta.
        $empresa_valor = null;
        $individual_valor = null;
        $eliminar_plan = false;

        // Verificar y asignar valor vacío si 'response' no está presente en la solicitud
        if (!$request->has('response')) {
            $request->merge(['response' => '']);
        }
        // Verificar y asignar valor vacío si 'selected_option' no está presente en la solicitud
        if (!$request->has('selected_option')) {
            $request->merge(['selected_option' => '']);
        }
        //Valida que tenga el campo seleccionado antes de enviar.
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'response' => 'nullable|string', //Respuesta preguntas abiertas.
            'selected_option' => 'nullable|string'
        ]);

        //Antes de guardar la pregunta, necesitamos validar si la pregunta (4) que es la de la edad, tenga más de 18 años.
        if($validated['question_id'] == 4){
            $validad_edad = validar_edad($validated['response']);
            if($validad_edad == false){
                return redirect()->route('survey.start')->with('error', 'Debe ser mayor de edad para hacer uso de nuestros servicios.');
            }
        }

        //Guardar la respues de la pregunta en la memoria cache
        session()->put('responses.' . $validated['question_id'], [
            'question_id' => $validated['question_id'],
            'response' => $validated['response'],
            'selected_option' => $validated['selected_option'],
        ]);

        // Crear la respuesta en función del tipo de pregunta (abierta o selección multiple)
        // Validar si la pregunta es la 12, para que guarde en otro lado, sino lo guarda normal.
        if($request->question_id == 12){
            //Guarda la respuesta en otra tabla.
            //Por defecto lleva a la pregunta final (correo electronico)
            $question = Question::where('id', '=', 6)->first();
        }else{
            // Lógica para determinar la siguiente pregunta.
            //Para validar que $request->selected_option no sea nulo ni una cadena vacía (''), puedes usar la función filled()
            if($request->filled('selected_option')){
                //La primera pregunta no esta en la tabla Options, sino en la de paises.
                if($validated['question_id'] == 1){
                    //Trae la siguiente pregunta según la opción.
                    $nextOption = Pais::find($validated['selected_option']);
                    $question = $nextOption->nextQuestion;
                }else{
                    //Trae la siguiente pregunta según la opción.
                    $nextOption = Option::find($validated['selected_option']);
                    $question = $nextOption->nextQuestion;
                }
            }else{
                //Trae la siguiente pregunta mayor al ID de la pregunta anterior. - Por lo tanto en la base de datos debe estar secuencial.
                $question = Question::where('id', '>', $request->question_id)->first();
                //FALTA LA OTRA MANERA DE GUARDADO.
            }
        }

        $tipo_pregunta = $question->type;
        //Valida si es la última pregunta para guardar los datos almacenados en la sesión.
        if($request->question_id == 6){ //Pregunta del correo electrónico.
            $variables = session()->get('responses');
            //Crear el usuario
            $email = $request->response;
            $name = explode('@', $email)[0]; // Obtiene la parte del correo antes del arroba
            // Buscar usuario por correo electrónico
            $user = User::where('email', $email)->first();
            if(!$user){
                // Si el usuario no existe, crearlo
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('123456789'), // Hashea la contraseña '123456789'
                ]);
            }
            //Comienza a guardar las respuestas.
            foreach ($variables as $response) {
                if ($response['selected_option']){
                    if($response['question_id'] == 1){
                        //Pregunta del país
                        CountryResponse::create([
                            'user_id' => $user->id,
                            'question_id' => $response['question_id'],
                            'pais_id' => $response['selected_option'],
                        ]);
                    }else{
                        //Preguntas de selección múltiple
                        SurveyResponse::create([
                            'user_id' => $user->id,
                            'question_id' => $response['question_id'],
                            'option_id' => $response['selected_option'],
                        ]);
                    }
                    //Solo se activa con preguntas de seleccioón multiple.
                    $recommendedPlan = recomendar_plan($empresa_valor, $individual_valor, $response['selected_option']);
                    //Elige el plan individual a eliminar (solo aplica para individuales)
                    //$eliminar_plan = eliminar_plan($eliminar_plan, $response['question_id'],$response['selected_option'],$recommendedPlan['individual'] );
                } else if ($response['response']) {
                    OpenResponse::create([
                        'user_id' => $user->id,
                        'question_id' => $response['question_id'],
                        'response' => $response['response'],
                    ]);
                }
            }
            // Limpiar la sesión después de procesar las respuestas
            session()->forget('responses');
        }

        //Redirigir a la vista de planes.
        $tipo_pregunta = $question->type;
        // Valida si es la última pregunta para redirigir a otra vista - Pregunta del correo electrónico.
        if ($request->question_id == 6){
            //Validar si se dirige por el camino de empresas o de individuales.
            if($recommendedPlan['individual'] == null){
                //Va por el camino de empresas
                $PlanRecomendado = $recommendedPlan['empresa'];
                // Llamar la función para enviar el correo
                $this->enviarCorreo();
                return redirect('/')->with('mensaje', 'pop_up_test');
            }else{
                //Traer los planes.
                $planes = Plan::where('tipo', '=', 'individual')->get();
                //Va por el camino de individuales
                $PlanRecomendado = $recommendedPlan['individual'];
                //Eliminar el plan
                $bandera_espana = false;
                $eliminar_plan = eliminar_plan_extra($variables, $PlanRecomendado, $bandera_espana);
                //Validar si escogió españa, y si le recomienda el Flex, cambiarlo a Full, porque se elimina el flex
                if($eliminar_plan['espana'] == true){
                    if($PlanRecomendado == 'Flex'){
                        $PlanRecomendado = 'Full'; 
                    }
                }
                $nombre_a_eliminar = $eliminar_plan['plan_eliminar'];
                $planes_actualizados = eliminar_plan_del_arreglo($planes, $nombre_a_eliminar);
                return redirect('/')->with('mensaje', 'pop_up_test');
            }
        }else{
            // Valida que se tenga la pregunta para luego enviarla a la vista, sino se terminó y lo dirige a la vista de completado.
            return $question
            ? view('survey.survey', compact('question', 'tipo_pregunta'))
            : redirect('/')->with('mensaje', 'pop_up_test');
        }
    }

    public function complete(Request $request){
        //Traer los planes.
        $planes = Plan::where('tipo', '=', 'individual')->get();
        $nombre_a_eliminar = 'Full';
        $planes_actualizados = eliminar_plan_del_arreglo($planes, $nombre_a_eliminar);
        $PlanRecomendado = '';
        return view('survey.complete_individual',compact('PlanRecomendado','planes_actualizados'));
    }

    public function showCompleteIndividual($recommendedPlan){
        return view('survey.complete_individual', compact('recommendedPlan'));
    }

    public function inicio(){
        // Aquí puedes agregar lógica adicional para determinar la página final
        return view('home');
    }

    //Al completar la ùltima pregunta del correo, manda un email
    public function enviarCorreo(){
        $correo = OpenResponse::orderBy('id', 'desc')->first();
        $correo_nuevo = $correo->response;
        $destinatario1 = "info@medbyte.ai";  //info@medbyte.ai
        $destinatario2 = "vladimirmontes192@hotmail.com"; 
        $mensaje = "Este es un correo de prueba.";
        Mail::to($destinatario1)
            ->cc($destinatario2) //Copia
            ->send(new NotificacionCorreo($mensaje, $correo_nuevo));
    }

}

//Según las respuestas del cliente, se le recomienda un plan.
function recomendar_plan($empresa, $individual, $respuesta){
    //INDIVIDUALES
        // 1 - MIA GRATIS - Plan gratuito___Mia
        // 2 - Anytime___Anytime
        // 3 - Flex --- Flex
        // 4 - Full - Plus___Full
        // 5 - Pro - Premium___Pro

    //EMPRESAS
        // 6 - Medbyte Care - Personalizable Care_EMPRESAS
        // 7 - Medbyte Anytime Anytime_EMPRESAS
        // 8 - Medbyte Full - Plus Full_EMPRESAS

    //Sigue la recomendación de planes individuales
    if($respuesta == 9){
        $individual = 'Mia';
    } else if($respuesta == 13){
        $individual = 'Anytime';
    } else if($respuesta == 12){
        $individual = 'Pro';
    }else if($respuesta == 15){
        $individual = 'Flex';
    }else if($respuesta == 19){
        $individual = 'Pro';
    }
    //Sigue la recomendación de planes para empresas
    else if($respuesta == 20){
        $empresa = 'Care_EMPRESAS';
    }else if($respuesta == 22){
        $empresa = 'Anytime_EMPRESAS';
    }else if($respuesta == 23){
        $empresa = 'Full_EMPRESAS';
    }else if($respuesta == 6){
        $empresa = 'Care_EMPRESAS';
    }
    // Retornar ambas variables en un array asociativo
    return [
        'empresa' => $empresa,
        'individual' => $individual
    ];
}

//En la pregunta de la edad validar que > 18, sino muestra una alerta.
function validar_edad($respuesta){
    if($respuesta >= 18){
        return true;
    }else{
        return false;
    }
}

//Según el plan que se recomiende, se debe borrar otro.
//Para no mostrar 5 planes, solo aplica para cuando es españa.
function eliminar_plan_extra($respuestas,$plan_recomendado, $bandera_espana){
    $plan_eliminar = '';
    foreach ($respuestas as $respuesta){
        //Significa que seleccionó españa.
        if($respuesta['question_id'] == 1 && $respuesta['selected_option'] == 186){
            $bandera_espana = true;
        }
    }
    if($bandera_espana == true){
        // -> Recomienda Pro - Quitar Full
        // -> Recomienda Anytime -> Quitar Pro
        // -> Recomienda Full -> Quitar Gratís
        // -> Recomienda Plus -> Quitar Anytime
        if($plan_recomendado == 'Mia'){
            $plan_eliminar = 'Full';
        } else if($plan_recomendado == 'Anytime'){
            $plan_eliminar = 'Pro';
        }else if($plan_recomendado == 'Pro'){
            $plan_eliminar = 'Full';
        }else if($plan_recomendado == 'Full'){
            $plan_eliminar = 'Mia';
        }
        //Porque, la idea es recomendar el full y no el flex cuando este en españa.
        else if($plan_recomendado == 'Flex'){
            $plan_eliminar = 'Flex';
        }
    }else{
        //Validar ¿No selecciono españa? Eliminar Full.
        //186 - id de españa
        $plan_eliminar = 'Full';
    }
    // Retorna un array asociativo con ambos valores
    return [
        'plan_eliminar' => $plan_eliminar,
        'espana' => $bandera_espana
    ];
}

//Recibe el arreglo de planes y el plan a eliminar.
function eliminar_plan_del_arreglo($planes, $nombre) {
    // Filtrar el arreglo para excluir el plan con el nombre dado
    $planes_filtrados = $planes->filter(function ($plan) use ($nombre) {
        return $plan->nombre !== $nombre;
    });

    // Reindexar el arreglo para eliminar los huecos en los índices
    return $planes_filtrados->values();
}
