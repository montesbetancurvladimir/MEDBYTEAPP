<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Onboarding;
use App\Models\ScoreResult;
use Illuminate\Http\Request;
use App\Models\TestmedbytePregunta;
use App\Models\TestmedbyteTipoPregunta;
use App\Models\TestmedbyteResult;
use App\Models\TestmedbyteRespuesta;
use App\Models\FactorImportancia;
use Carbon\Carbon;

class TestMedbyteController extends Controller
{

    /*
        Inicio_P3 - question1: "1. Durante el último mes , ¿Cuántos días has tenido poco interés o placer en hacer las cosas?"
        P4 - question2: "2. Durante el último mes , ¿Cuántos días te has sentido decaído(a), deprimido(a) o sin esperanzas?"
        P7 - question3: "3. Durante el último mes , ¿Cuántos días has tenido cansancio extremo o poca energía?"
        P9 - question4: "4. Durante el último mes , ¿Cuántos días has tenido dificultad en conciliar el sueño o has dormido en exceso?"
        P11 - question5: "5. Durante el último mes , ¿Cuántos días te has sentido mal contigo mismo(a) que eres un fracaso(a) o que has quedado mal con tu familia?"
        P12 - question6: "6. Durante el último mes , ¿Cuántos días has tenido tendencia a beber o fumar más de lo habitual?"
        P16 - question7: "7. Durante el último mes , ¿Cuántos días has sentido pinchazos o sensaciones dolorosas en distintas partes del cuerpo?"
        P18 - question8: "8. Durante el último mes , ¿Cuántos días has tenido temblores musculares (por ejemplo, tics nerviosos o parpadeos)?"
        OMITIR P_edad - question9: "9. ¿Cuántos años tienes?" OMITIR
        Peducativo - question10: "10. ¿Cuál es tu nivel educativo?"
        Continuacion_P19 - question11: "11. ¿De cuántas personas se compone su hogar?"
        P20 - question12: "12. ¿Cuántas personas tienes a cargo?"
        P22 - question13: "13. En caso de tener una relación sentimental, ¿Cómo ves para ti esta relación sentimental?"
        P25 - question14: "14. ¿Tienes algún familiar que haya sufrido de algún trastorno mental?"
        P29 - question15: "15. A lo largo del último mes, ¿Cuántos días has consumido licor?"
        P30 - question16: "16. En un período de 30 días, ¿Cuántos días consumes sustancias psicoactivas?"
        P31 - question17: "17. Durante el últimos seis meses , ¿Has recibido tratamiento psicológico?"
        P32 - question18: "18. Durante el último año , ¿Has tenido tratamiento psiquiátrico?"
        P33 - question19: "19. ¿Tienes alguna enfermedad cardíaca?"
        P36 - question20: "20. ¿Has atentado físicamente contra tu cuerpo?"
        P36_R_Cierre - question21: "21. ¿Sufres de alguna enfermedad crónica que te cause dolor o incapacidad?"
    */
    //Listado de preguntas del test
    public function preguntas(){
        $listado_preguntas = listado_preguntas();  // Inicializar el arreglo para las preguntas
        //Eliminar la última pregunta, que es la edad.
        array_pop($listado_preguntas);
        // Devolver el listado de preguntas como respuesta JSON
        return response()->json($listado_preguntas, 200);
    }

    //Guardar resultados del test
    public function store(Request $request){
        date_default_timezone_set('America/Bogota');
        $user =  auth()->id();
        $fecha_actual = Carbon::now()->format('Y-m-d');
        $respuestas = $request->all();
        $respuestasJson = json_encode($respuestas);
        //Validar que la pregunta de la edad ya esté registrada en el onboarding.
        $edad = validarPreguntaEdadOnboarding($user);
        //Si trae la edad, la junta a las respuestas, sino retorna un error
        if ($edad !== null) {
            $edad_final = [
                'pregunta' => [
                    'id' => 9,
                    'variable' => "P_edad",
                    'pregunta' => "question9",
                    'texto' => "¿Cuántos años tienes?"
                ],
                'respuesta' => [
                    'texto' => $edad,
                ]
            ];
        } else {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no ha realizado el onboarding.',
                'error_code' => 'ONBOARDING_NO_ENCONTRADO'
            ], 404);
        }
        $resultados_test = depurador_test($respuestas);
        //Le añade la respuesta al final.
        $resultados_test[] = $edad_final; 
        $formulario_analisis = procesarRespuestas($resultados_test);

        $P1= $formulario_analisis['question1']['respuesta'] ?? null;
        $P2= $formulario_analisis['question2']['respuesta'] ?? null;
        $P3= $formulario_analisis['question3']['respuesta'] ?? null;
        $P4= $formulario_analisis['question4']['respuesta'] ?? null;
        $P7= $formulario_analisis['question5']['respuesta'] ?? null;
        $P9= $formulario_analisis['question6']['respuesta'] ?? null;
        $P11= $formulario_analisis['question7']['respuesta'] ?? null;
        $P12= $formulario_analisis['question8']['respuesta'] ?? null;
        $P16= $formulario_analisis['question9']['respuesta'] ?? null;
        $P18= $formulario_analisis['question10']['respuesta'] ?? null;
        $P19= $formulario_analisis['question11']['respuesta'] ?? null;
        $P20= $formulario_analisis['question12']['respuesta'] ?? null;
        $P22= $formulario_analisis['question13']['respuesta'] ?? null;
        $P25= $formulario_analisis['question14']['respuesta'] ?? null;
        $P26= $formulario_analisis['question21']['respuesta'] ?? null;
        $P29= $formulario_analisis['question15']['respuesta'] ?? null;
        $P31= $formulario_analisis['question17']['respuesta'] ?? null;
        $P32= $formulario_analisis['question18']['respuesta'] ?? null;
        $P33= $formulario_analisis['question19']['respuesta'] ?? null;
        $P36= $formulario_analisis['question20']['respuesta'] ?? null;

        $Ansiedad = scoreAnsiedad($P2 , $P4 , $P7 , $P9 , $P11 , $P12 , $P16 , $P18 , $P19 , $P29 , $P31 , $P32);
        $scoreAnsiedad = round($Ansiedad['0'],1);
        $nivelRiesgoAnsiedad =  $Ansiedad['1'];

        $Estres = scoreStress ($P1 , $P3 , $P4 , $P12 , $P16 , $P20 , $P25 , $P26 , $P32 , $P36);
        $scoreStress = round($Estres['0'],1);
        $nivelRiesgoStress = $Estres['1'];

        $mensajesAlerta= mensajesScoreTotal($P2 , $P4 , $P7 , $P9 , $P12 , $P19 , $P20 , $P22 , $P29 , $P31 , $P32 , $P33 , $P36);
        //Los mensajes guardan como un array, entonces se convierte en JSON para que guarde en el campo alertas.
        $mensajesAlerta_FIN = json_encode($mensajesAlerta);

        $scoreTotal = 0.5 * $scoreAnsiedad + 0.5 * $scoreStress;
        if( $scoreTotal >=0 && $scoreTotal <27.5 ){ $nivelRiesgoTotal=5;}
        if( $scoreTotal >=27.5 && $scoreTotal <57 ){ $nivelRiesgoTotal=4;}
        if( $scoreTotal >=57 && $scoreTotal <72.6 ){ $nivelRiesgoTotal=3;}
        if( $scoreTotal >=72.6 && $scoreTotal <79.9 ){ $nivelRiesgoTotal=2;}
        if( $scoreTotal >=79.9 && $scoreTotal <85.7 ){ $nivelRiesgoTotal=2;}
        if( $scoreTotal >=85.7 && $scoreTotal <89.6 ){ $nivelRiesgoTotal=2;}
        if( $scoreTotal >=89.6 && $scoreTotal <90.9 ){ $nivelRiesgoTotal=2;}
        if( $scoreTotal >=90.9 && $scoreTotal <95.5 ){ $nivelRiesgoTotal=2;}
        if( $scoreTotal >=95.5 && $scoreTotal <97.2 ){ $nivelRiesgoTotal=1;}
        if( $scoreTotal >=97.2 && $scoreTotal <100 ){ $nivelRiesgoTotal=1;}

        //Registrar Test y Alertas
        $test = TestmedbyteResult::create([
            'user_id' => $user,
            'respuestas'=>$respuestasJson,
            'tipo'=>'inicial',
            'nivel_riesgo'=>$nivelRiesgoTotal,
            'fecha_aplicacion'=>$fecha_actual,
        ]);
        //Registrar Score
        $score = ScoreResult::create([
            'user_id' => $user,
            'nivel_riesgo_id'=>$nivelRiesgoTotal,
            'test_id'=>$test->id,
            'score_total'=>$scoreTotal,
            'score_ansiedad'=>$scoreAnsiedad,
            'score_estres'=>$scoreStress,
            'alerta_medbyte'=>$mensajesAlerta_FIN,
            'fecha_aplicacion'=>$fecha_actual,
        ]);

        //Calcular factores de importancia.
        $factor_importancia = salud_mental_individuo($user);
        FactorImportancia::create([
            'user_id' => $user,
            //Estres
            'scoreStress'=>$factor_importancia[0]['scoreStress'],
            'nivelRiesgoStress'=>$factor_importancia[0]['nivelRiesgoStress'],
            'P1_WOE_Stress'=>$factor_importancia[0]['P1_WOE_Stress'],
            'P3_WOE_Stress'=>$factor_importancia[0]['P3_WOE_Stress'],
            'P4_WOE_Stress'=>$factor_importancia[0]['P4_WOE_Stress'],
            'P12_WOE_Stress'=>$factor_importancia[0]['P12_WOE_Stress'],
            'P16_WOE_Stress'=>$factor_importancia[0]['P16_WOE_Stress'],
            'P20_WOE_Stress'=>$factor_importancia[0]['P20_WOE_Stress'],
            'P25_WOE_Stress'=>$factor_importancia[0]['P25_WOE_Stress'],
            'P32_WOE_Stress'=>$factor_importancia[0]['P32_WOE_Stress'],
            'P36_WOE_Stress'=>$factor_importancia[0]['P36_WOE_Stress'],
            'P26_WOE_Stress'=>$factor_importancia[0]['P26_WOE_Stress'],
            //Ansiedad
            'scoreAnsiedad'=>$factor_importancia[1]['scoreAnsiedad'],
            'nivelRiesgoAnsiedad'=>$factor_importancia[1]['nivelRiesgoAnsiedad'],
            'P2_WOE_Anxiety'=>$factor_importancia[1]['P2_WOE_Anxiety'],
            'P4_WOE_Anxiety'=>$factor_importancia[1]['P4_WOE_Anxiety'],
            'P7_WOE_Anxiety'=>$factor_importancia[1]['P7_WOE_Anxiety'],
            'P9_WOE_Anxiety'=>$factor_importancia[1]['P9_WOE_Anxiety'],
            'P11_WOE_Anxiety'=>$factor_importancia[1]['P11_WOE_Anxiety'],
            'P12_WOE_Anxiety'=>$factor_importancia[1]['P12_WOE_Anxiety'],
            'P16_WOE_Anxiety'=>$factor_importancia[1]['P16_WOE_Anxiety'],
            'P18_WOE_Anxiety'=>$factor_importancia[1]['P18_WOE_Anxiety'],
            'P19_WOE_Anxiety'=>$factor_importancia[1]['P19_WOE_Anxiety'],
            'P29_WOE_Anxiety'=>$factor_importancia[1]['P29_WOE_Anxiety'],
            'P31_WOE_Anxiety'=>$factor_importancia[1]['P31_WOE_Anxiety'],
            'P32_WOE_Anxiety'=>$factor_importancia[1]['P32_WOE_Anxiety'],
            //Mensajes
            'mensaje_1'=>$factor_importancia[3]['mensaje_1'],
            'mensaje_2'=>$factor_importancia[3]['mensaje_2'],
            'mensaje_3'=>$factor_importancia[3]['mensaje_3'],
            'mensaje_4'=>$factor_importancia[3]['mensaje_4'],
            'mensaje_5'=>$factor_importancia[3]['mensaje_5'],
            //Total
            'scoreTotal'=>$factor_importancia[2]['scoreTotal'],
            'nivelRiesgoTotal'=>$factor_importancia[2]['nivelRiesgoTotal'],
            'P1_Total_WOE'=>$factor_importancia[4]['P1_Total_WOE'],
            'P2_Total_WOE'=>$factor_importancia[4]['P2_Total_WOE'],
            'P3_Total_WOE'=>$factor_importancia[4]['P3_Total_WOE'],
            'P4_Total_WOE'=>$factor_importancia[4]['P4_Total_WOE'],
            'P7_Total_WOE'=>$factor_importancia[4]['P7_Total_WOE'],
            'P9_Total_WOE'=>$factor_importancia[4]['P9_Total_WOE'],
            'P11_Total_WOE'=>$factor_importancia[4]['P11_Total_WOE'],
            'P12_Total_WOE'=>$factor_importancia[4]['P12_Total_WOE'],
            'P16_Total_WOE'=>$factor_importancia[4]['P16_Total_WOE'],
            'P18_Total_WOE'=>$factor_importancia[4]['P18_Total_WOE'],
            'P19_Total_WOE'=>$factor_importancia[4]['P19_Total_WOE'],
            'P20_Total_WOE'=>$factor_importancia[4]['P20_Total_WOE'],
            'P25_Total_WOE'=>$factor_importancia[4]['P25_Total_WOE'],
            'P29_Total_WOE'=>$factor_importancia[4]['P29_Total_WOE'],
            'P31_Total_WOE'=>$factor_importancia[4]['P31_Total_WOE'],
            'P32_Total_WOE'=>$factor_importancia[4]['P32_Total_WOE'],
            'P36_Total_WOE'=>$factor_importancia[4]['P36_Total_WOE'],
            'P26_Total_WOE'=>$factor_importancia[4]['P26_Total_WOE']
        ]);
        
        return response()->json([
            'test' => $test,
            'score' => $score,
            'factor_importancia' => 'OK'
        ], 200);
    }

    //Muestra el resultado del test del usuario.
    public function score_calculado(){
        $user = auth()->id();
        $score = ScoreResult::where('score_results.user_id', '=', $user)
        ->join('nivel_riesgos', 'nivel_riesgos.id', '=', 'score_results.nivel_riesgo_id')
        ->select('score_results.*', 'nivel_riesgos.nombre AS nombre_nivel_riesgo') 
        ->orderBy('score_results.id', 'desc') // Ordena por la columna 'id' en orden descendente
        ->get()
        ->makeHidden(['created_at', 'updated_at','id','user_id'])
        ->first(); // Obtiene el primer registro después de ordenar
        // Retornar una respuesta exitosa
        return response()->json($score, 201);
    }
}

//Hace el listado de las preguntas con sus respectivas respuestas
function listado_preguntas(){
    $arreglo = [];
    $preguntas = TestmedbytePregunta::select('testmedbyte_preguntas.*', 'testmedbyte_tipo_preguntas.nombre as tipo_pregunta')
        ->leftJoin('testmedbyte_tipo_preguntas', 'testmedbyte_preguntas.tipo_pregunta_id', '=', 'testmedbyte_tipo_preguntas.id')
        ->get();
    foreach ($preguntas as $pregunta) {  
        // Obtener las respuestas para la pregunta actual
        $respuestas = TestmedbyteRespuesta::where('test_pregunta_id', $pregunta->id)
            ->get()
            ->makeHidden(['created_at', 'updated_at'])
            ->toArray();
        // Agregar la pregunta y sus respuestas al array
        $arreglo[] = [
            'pregunta' => $pregunta,
            'respuestas' => $respuestas
        ];
    }
    return $arreglo;
}

//Califica los resultados del test realizado por el usuario
function depurador_test($respuestas){
    $resultados = [];
    //Por cada pregunta y respuesta extrae su valor y lo añade al arreglo
    for ($i=0; $i < count($respuestas); $i++){ 
        $pregunta = TestmedbytePregunta::select('testmedbyte_preguntas.*', 'testmedbyte_tipo_preguntas.nombre as tipo_pregunta')
            ->leftJoin('testmedbyte_tipo_preguntas', 'testmedbyte_preguntas.tipo_pregunta_id', '=', 'testmedbyte_tipo_preguntas.id')
            ->where('testmedbyte_preguntas.id', $respuestas[$i]['id_pregunta']) 
            ->first();
        $respuesta = TestmedbyteRespuesta::where('test_pregunta_id', $pregunta->id)
            ->where('testmedbyte_respuestas.id', $respuestas[$i]['id_respuesta']) 
            ->first();
        // Agregar la pregunta y sus respuestas al array
        $resultados[] = [
            'pregunta' => [
                'id' => $pregunta->id,
                'variable' => $pregunta->variable,
                'pregunta' => $pregunta->nombre,
                'texto' => $pregunta->texto
            ],
            'respuesta' => [
                'id' => $respuesta->id,
                'texto' => $respuesta->respuesta,
            ]
        ];
    }
    return $resultados;
}

//Valida que tenga el Onboarding realizado y busca la edad.
function validarPreguntaEdadOnboarding($user) {
    // Validar que la pregunta de la edad ya esté registrada en el onboarding.
    $onboarding = Onboarding::select('onboardings.*')
        ->where('onboardings.user_id', $user)  
        ->first();
    if (isset($onboarding)) {
        $respuestas_on = json_decode($onboarding->respuestas, true); // Decodifica el JSON como un array asociativo
        $edad = $respuestas_on[2]['respuesta_abierta'];
    } else {
        $edad = null;
    }
    return $edad;
}

//toma el array de respuestas y lo transforma en un array asociativo donde las claves son los ID de las preguntas (variable) y los valores son las respuestas (texto).
function procesarRespuestas($respuestas) {
    $resultado = [];
    foreach ($respuestas as $respuesta) {
        // Extrae los datos necesarios
        $idPregunta = $respuesta['pregunta']['variable'];
        $variablePregunta = $respuesta['pregunta']['pregunta']; // Valor de la pregunta (ej. "question1")
        $textoRespuesta = $respuesta['respuesta']['texto'];
        
        // Asigna tanto la pregunta como la respuesta a variables basadas en el ID de la pregunta
        $resultado[$variablePregunta] = [
            'variable' => $idPregunta,
            'respuesta' => $textoRespuesta
        ];
    }
    return $resultado;
}

function scoreAnsiedad ($P2 , $P4 , $P7 , $P9 , $P11 , $P12 , $P16 , $P18 , $P19 , $P29 , $P31 , $P32 ){
    $fa = 0;
    $pa = 0;
    $nivelRiesgoAnsiedad = '';
    //2 - ¿Durantfe el último mes cuántas veces te has sentido decaído(a), deprimido(a) o sin esperanzas?
    if($P2=='0 veces al mes'){ $P2_WOE = 1.685043;}
    if($P2=='1-15 veces al mes'){ $P2_WOE = 0.119869;}
    if($P2=='16-25 veces al mes'){ $P2_WOE = -1.316009;}
    if($P2=='26-30 veces al mes'){ $P2_WOE = -3.222698;}
    //4 - ¿Durante el último mes cuántas veces has tenido dificultad en conciliar el sueño o has dormido en exceso.?
    if($P4=='0 veces al mes'){ $P4_WOE = 1.2687431;}
    if($P4=='1-15 veces al mes'){ $P4_WOE = 0.1039352;}
    if($P4=='16-25 veces al mes'){ $P4_WOE = -0.6890015;}
    if($P4=='26-30 veces al mes'){ $P4_WOE = -1.3255783;}
    //7 - ¿Durante el último mes cuántas veces te has sentido mal contigo mismo(a) – que eres un fracaso(a) o que has quedado mal con tu familia?
    if($P7=='0 veces al mes'){ $P7_WOE = 1.43730437;}
    if($P7=='1-15 veces al mes'){ $P7_WOE = -0.08922837;}
    if($P7=='16-25 veces al mes'){ $P7_WOE = -1.23596615;}
    if($P7=='26-30 veces al mes'){ $P7_WOE = -2.43993896;}
    //9 - ¿Durante el último mes cuántas veces has tenido tendencia a beber o fumar mas de lo habitual?
    if($P9=='0 veces al mes'){ $P9_WOE = 0.3641484;}
    if($P9=='1-15 veces al mes'){ $P9_WOE = -0.1018029;}
    if($P9=='16-25 veces al mes'){ $P9_WOE = -1.2767881;}
    if($P9=='26-30 veces al mes'){ $P9_WOE = -2.8862261;}
    //11 - ¿Durante el último mes cuántas veces has sentido pinchazos o sensaciones dolorosas en distintas partes del cuerpo?
    if($P11=='0 veces al mes'){ $P11_WOE = 0.863278;}
    if($P11=='1-15 veces al mes'){ $P11_WOE = -0.4294903;}
    if($P11=='16-25 veces al mes'){ $P11_WOE = -2.1034667;}
    if($P11=='26-30 veces al mes'){ $P11_WOE = -1.8364039;}
    //12 - ¿Durante el último mes cuántas veces has tenido temblores musculares (por ejemplo, tics nerviosos o parpadeos)?
    if($P12=='0 veces al mes'){ $P12_WOE = 0.9490098;}
    if($P12=='1-15 veces al mes'){ $P12_WOE = -0.4918334;}
    if($P12=='16-25 veces al mes'){ $P12_WOE = -1.8958274;}
    if($P12=='26-30 veces al mes'){ $P12_WOE = -1.9699353;}
    //16 - Edad
    if( $P16>=0 && $P16 <=14 ){ $P16_WOE=0;}
    if( $P16>=15 && $P16 <=21 ){ $P16_WOE=-0.22073547;}
    if( $P16>=22 && $P16 <=23 ){ $P16_WOE=-0.11363734;}
    if( $P16>=24 && $P16 <=25 ){ $P16_WOE=-0.48833079;}
    if( $P16>=26 && $P16 <=26 ){ $P16_WOE=-0.46585793;}
    if( $P16>=27 && $P16 <=28 ){ $P16_WOE=-0.11363734;}
    if( $P16>=29 && $P16 <=31 ){ $P16_WOE=0.49087377;}
    if( $P16>=32 && $P16 <=33 ){ $P16_WOE=0.70421332;}
    if( $P16>=34 && $P16 <=39 ){ $P16_WOE=-0.15285805;}
    if( $P16>=40 && $P16 <=47 ){ $P16_WOE=-0.04002552;}
    if( $P16>=48 && $P16 <=68 ){ $P16_WOE=0.85144356;}
    //18 - ¿Cual es tu nivel educativo?
    if($P18=='Primaria'){ $P18_WOE = 0;}
    if($P18=='Secundaria (o Bachillerato)'){ $P18_WOE = -0.2522838;}
    if($P18=='Formación profesional (Técnica)'){ $P18_WOE = 0.1445975;}
    if($P18=='Tecnólogo'){ $P18_WOE = 0.1445975;}
    if($P18=='Profesional'){ $P18_WOE = 0.1388338;}
    if($P18=='Posgrado'){ $P18_WOE = -0.3117073;}
    //19 - ¿Con cuántas personas convives actualmente?
    if($P19=='CERO'){ $P19_WOE = 0.10950621;}
    if($P19=='UNO'){ $P19_WOE = -0.09116448;}
    if($P19=='DOS'){ $P19_WOE = 0.10053754;}
    if($P19=='TRES'){ $P19_WOE = -0.3692311;}
    if($P19=='CUATRO'){ $P19_WOE = -0.17817586;}
    if($P19=='CINCO'){ $P19_WOE = 0;}
    if($P19=='6 o más'){ $P19_WOE = 1.36226918;}
    //29 - ¿Cuántas veces consumes alcohol en un mes?
    if($P29=='CERO'){ $P29_WOE = 0.1746455;}
    if($P29=='UNO'){ $P29_WOE = -0.1018029;}
    if($P29=='DOS'){ $P29_WOE = 0.5315006;}
    if($P29=='TRES'){ $P29_WOE = 0.6059431;}
    if($P29=='CUATRO'){ $P29_WOE = -0.7171724;}
    if($P29=='CINCO'){ $P29_WOE = -0.4013194;}
    if($P29=='6 o más'){ $P29_WOE = -0.9513657;}
    //31 - ¿Has recibido tratamiento psicológico?
    if($P31=='No'){ $P31_WOE = 0.7863929;}
    if($P31=='Si'){ $P31_WOE = -0.8873234;}
    //32 - ¿Has tenido tratamiento psiquiátrico?
    if($P32=='No'){ $P32_WOE = 0.4878203;}
    if($P32=='Si'){ $P32_WOE = -2.6117892;}
    $fa = $P2_WOE * 0.395
    + $P4_WOE * 0.46
    + $P7_WOE * 0.4786
    + $P9_WOE * 0.6469
    + $P11_WOE * 0.7129
    + $P12_WOE * 0.3762
    + $P16_WOE * 1.3969
    + $P18_WOE * 1.2349
    + $P19_WOE * -2.4087
    + $P29_WOE * 0.5845
    + $P31_WOE * 0.9462
    + $P32_WOE * 1.422
    + 0.9864;
    $pa = 1 / (1 + exp(-$fa));
    $scoreAnsiedad = $pa * 100;
    if( $scoreAnsiedad >=0 && $scoreAnsiedad <17.508 ){ $nivelRiesgoAnsiedad ='Muy Alto';}
    if( $scoreAnsiedad >=17.508 && $scoreAnsiedad <57.825 ){ $nivelRiesgoAnsiedad ='Alto';}
    if( $scoreAnsiedad >=57.825 && $scoreAnsiedad <78.577 ){ $nivelRiesgoAnsiedad ='Medio';}
    if( $scoreAnsiedad >=78.577 && $scoreAnsiedad <88.886 ){ $nivelRiesgoAnsiedad ='Bajo';}
    if( $scoreAnsiedad >=88.886 && $scoreAnsiedad <93.661 ){ $nivelRiesgoAnsiedad ='Bajo';}
    if( $scoreAnsiedad >=93.661 && $scoreAnsiedad <96.831 ){ $nivelRiesgoAnsiedad ='Bajo';}
    if( $scoreAnsiedad >=96.831 && $scoreAnsiedad <98.334 ){ $nivelRiesgoAnsiedad ='Bajo';}
    if( $scoreAnsiedad >=98.334 && $scoreAnsiedad <98.973 ){ $nivelRiesgoAnsiedad ='Bajo';}
    if( $scoreAnsiedad >=98.973 && $scoreAnsiedad <99.428 ){ $nivelRiesgoAnsiedad ='Muy Bajo';}
    if( $scoreAnsiedad >=99.428 && $scoreAnsiedad <100 ){ $nivelRiesgoAnsiedad ='Muy Bajo';}
    return array ($scoreAnsiedad , $nivelRiesgoAnsiedad);
}

function scoreStress ($P1 , $P3 , $P4 , $P12 , $P16 , $P20 , $P25 , $P26 , $P32 , $P36){

    $fs = 0;
    $ps = 0;
    $nivelRiesgoStress = '';

    //1 - ¿Durante el último mes cuántas veces has tenido poco interés o placer en hacer las cosas ?
    if($P1=='0 veces al mes'){ $P1_WOE = 0.96132577;}
    if($P1=='1-15 veces al mes'){ $P1_WOE = 0.02178973;}
    if($P1=='16-25 veces al mes'){ $P1_WOE = -0.70898014;}
    if($P1=='26-30 veces al mes'){ $P1_WOE = -0.7176382;}

    //3 - ¿Durante el último mes cuántas veces has tenido cansancio extremo o poca energía?
    if($P3=='0 veces al mes'){ $P3_WOE = 1.4105935;}
    if($P3=='1-15 veces al mes'){ $P3_WOE = 0.2942816;}
    if($P3=='16-25 veces al mes'){ $P3_WOE = -0.9934255;}
    if($P3=='26-30 veces al mes'){ $P3_WOE = -1.2461634;}

    //4 - ¿Durante el último mes cuántas veces has tenido dificultad en conciliar el sueño o has dormido en exceso.?
    if($P4=='0 veces al mes'){ $P4_WOE = 0.8299243;}
    if($P4=='1-15 veces al mes'){ $P4_WOE = 0.1341608;}
    if($P4=='16-25 veces al mes'){ $P4_WOE = -0.7976809;}
    if($P4=='26-30 veces al mes'){ $P4_WOE = -1.0873852;}

    //12 - ¿Durante el último mes cuántas veces has tenido temblores musculares (por ejemplo, tics nerviosos o parpadeos)?
    if($P12=='0 veces al mes'){ $P12_WOE = 0.5607083;}
    if($P12=='1-15 veces al mes'){ $P12_WOE = -0.3716872;}
    if($P12=='16-25 veces al mes'){ $P12_WOE = -1.1414524;}
    if($P12=='26-30 veces al mes'){ $P12_WOE = -2.1935447;}

    //16 - Edad
    if( $P16 < 15){ $P16 = 18;}
    if( $P16 >=15 && $P16 <=21 ){ $P16_WOE =0.7078769;}
    if( $P16 >=22 && $P16 <=23 ){ $P16_WOE =-0.2476346;}
    if( $P16 >=24 && $P16 <=25 ){ $P16_WOE =-0.4299561;}
    if( $P16 >=26 && $P16 <=26 ){ $P16_WOE =-0.1298515;}
    if( $P16 >=27 && $P16 <=28 ){ $P16_WOE =0.6125667;}
    if( $P16 >=29 && $P16 <=31 ){ $P16_WOE =-0.2476346;}
    if( $P16 >=32 && $P16 <=33 ){ $P16_WOE =-0.381166;}
    if( $P16 >=34 && $P16 <=39 ){ $P16_WOE =-0.1024526;}
    if( $P16 >=40 && $P16 <=47 ){ $P16_WOE =-0.2098942;}
    if( $P16 >=48 && $P16 <=68 ){ $P16_WOE =0.6433384;}

    //20 - ¿Cuántas personas tienes a cargo?
    if($P20=='CERO'){ $P20_WOE = 0.063578;}
    if($P20=='UNO'){ $P20_WOE = 0.4943028;}
    if($P20=='DOS'){ $P20_WOE = -0.6445159;}
    if($P20=='TRES'){ $P20_WOE = -0.7866311;}
    if($P20=='CUATRO'){ $P20_WOE = 1.0051284;}
    if($P20=='CINCO'){ $P20_WOE = 1.0051284;}
    if($P20=='6 o más'){ $P20_WOE = -1.6339289;}

    //25 - ¿Tienes algún familiar que haya sufrido de algún trastorno mental?
    if($P25=='No'){ $P25_WOE = 0.3168952;}
    if($P25=='Si'){ $P25_WOE = -0.4204774;}

    //26 - ¿Sufres de alguna enfermedad crónica que te cause dolor o incapacidad?
    if($P26=='No'){ $P26_WOE = 0.1200902;}
    if($P26=='Si'){ $P26_WOE = -0.8537704;}

    //32 - ¿Has tenido tratamiento psiquiátrico?
    if($P32=='No'){ $P32_WOE = 0.328845;}
    if($P32=='Si'){ $P32_WOE = -2.153804;}

    //36 - ¿Has atentado físicamente contra tu cuerpo?
    if($P36=='No'){ $P36_WOE = 0.05927886;}
    if($P36=='Si'){ $P36_WOE = -0.42995613;}


    $fs = $P1_WOE * 0.4397
    + $P3_WOE * 0.7383
    + $P4_WOE * 0.4351
    + $P12_WOE * 0.6074
    + $P16_WOE * 1.4001
    + $P20_WOE * 0.9466
    + $P25_WOE * 0.6792
    + $P26_WOE * 0.762
    + $P32_WOE * 1.1041
    + $P36_WOE * 2.4064
    + 0.7967;

    $ps = 1 / (1 + exp(-$fs));

    $scoreStress = $ps * 100;

    if( $scoreStress >=0 && $scoreStress <27 ){ $nivelRiesgoStress ='Muy Alto';}
    if( $scoreStress >=27 && $scoreStress <47.5 ){ $nivelRiesgoStress ='Alto';}
    if( $scoreStress >=47.5 && $scoreStress <65.3 ){ $nivelRiesgoStress ='Medio';}
    if( $scoreStress >=65.3 && $scoreStress <75 ){ $nivelRiesgoStress ='Bajo';}
    if( $scoreStress >=75 && $scoreStress <82.1 ){ $nivelRiesgoStress ='Bajo';}
    if( $scoreStress >=82.1 && $scoreStress <86.3 ){ $nivelRiesgoStress ='Bajo';}
    if( $scoreStress >=86.3 && $scoreStress <90.9 ){ $nivelRiesgoStress ='Bajo';}
    if( $scoreStress >=90.9 && $scoreStress <93.93 ){ $nivelRiesgoStress ='Bajo';}
    if( $scoreStress >=93.93 && $scoreStress <99.44 ){ $nivelRiesgoStress ='Muy Bajo';}
    if( $scoreStress >=99.44 && $scoreStress <100 ){ $nivelRiesgoStress ='Muy Bajo';}


    return array ($scoreStress , $nivelRiesgoStress);

}

function mensajesScoreTotal ($P2 , $P4 , $P7 , $P9 , $P12 , $P19 , $P20 , $P22 , $P29 , $P31 , $P32 , $P33 , $P36){

    $msg1 = '';
    $msg2 = '';
    $msg3 = '';
    $msg4 = '';
    $msg5 = '';
    
    //P2 =IF(OR($C3="16-25 veces al mes",$C3= "26-30 veces al mes"),1,0)
    //P31 =IF($C19="Si",1,0)
    //P36 =IF($C22="Si",1,0)
    // Mensaje 1
    if (($P2 == '16-25 veces al mes' || $P2== '26-30 veces al mes') && $P31=='SI' && $P36 =='SI'){
    $msg1 = 'Alerta: Riesgo de volver a tener autolesiones físicas. Acciones: 1) Intervenir preguntando estado de ánimo, 2) Antes de 48 horas llamada telefónica del psicólogo verificando estado de ánimo y 3) Monitoreo de evolución por una semana.';
    }
    
    //P4 =IF(OR($C5="16-25 veces al mes",$C5= "26-30 veces al mes"),1,0)
    //P12 =IF(OR($C9="16-25 veces al mes",$C9= "26-30 veces al mes"),1,0)
    //P20 =IF(OR($C14="TRES",$C14= "CUATRO"),1,0)
    
    if(($P4 == '16-25 veces al mes' || $P4 == '26-30 veces al mes') && ($P12 =='16-25 veces al mes' || $P12 == '26-30 veces al mes') && ($P20 =='TRES' || $P20 == 'CUATRO' || $P20 =='CIMCO' || $P20 == '6 o más')){
    $msg2 = 'Alerta: Persona con riesgo de estrés Acción: 1. Intervenir brindando apoyo, 2. Retomar conversación cada dos semanas.';
    }
    
    
    //P7 =IF(OR($C6="16-25 veces al mes",$C6= "26-30 veces al mes"),1,0)
    //P19 =IF($C13="CERO",1,0)
    //P20 =IF($C14="CERO",1,0)
    //P22 =IF($C15="No tengo una relación sentimental",1,0)
    
    if (($P7 == '16-25 veces al mes' || $P7 == '26-30 veces al mes') && $P19 =='CERO' && $P20 =='CERO' && $P22 =='No tengo una relación sentimental'){
    $msg3 = 'Alerta: Persona solitaria que puede carecer de red de apoyo y ser propenso a iniciar una depresión. Acción: Monitorear mínimo 1 vez al mes.';
    }
    
    
    //P4 =IF(OR($C5="16-25 veces al mes",$C5= "26-30 veces al mes"),1,0)
    //P9 =IF(OR($C7="16-25 veces al mes",$C7= "26-30 veces al mes"),1,0)
    //P22 =IF(C15="Muy Mala",1,0)
    
    if (($P4 == '6-25 veces al mes' || $P4 == '26-30 veces al mes') && ($P9 =='16-25 veces al mes' || $P9 == '26-30 veces al mes') && $P22 =='Muy Mala' ){
    $msg4 = 'Alerta: Principios de ansiedad Acciones: 1. Contacto de psicólogo y hacer diagnóstico, indagando por síntomas ansiosos y recomendarle al paciente tips para la ansiedad, 2. Intervenir a la semana para verificar proceso.';
    }
    
    
    //P29 =IF(OR($C17="TRES",$C17="CUATRO",$C17="CINCO",$C17="6 o más"),1,0)
    //P32 =IF($C20="Si",1,0)
    //P33 =IF($C21="Si",1,0)
    
    if (($P29 =='TRES' || $P29 =='CUATRO' || $P29 =='CINCO' || $P29 =='6 o más') && $P32 =='SI' && $P33 =='SI') {
    $msg5 = 'Alerta: Persona medicada con riesgo de variaciones o cambios emocionales bruscos. Acción: Intervenir por vía telefónica mínimo 2 veces al mes para monitorear riesgo.';
    }
    
    return array ($msg1 , $msg2, $msg3, $msg4, $msg5);
}

function estructurar_inicial_MULTI($name, $value){
    if($name == "Inicio_P3"){
        $nameR = "question1";
        $valueR = pregunta_cantidad($value);
    }else if($name == "P4"){
        $nameR = "question2";
        $valueR = pregunta_cantidad($value);
    }else if($name == "P7"){
        $nameR = "question3";
        $valueR = pregunta_cantidad($value);
    }else if($name == "P9"){
        $nameR = "question4";
        $valueR = pregunta_cantidad($value);
    }else if($name == "P11"){
        $nameR = "question5";
        $valueR = pregunta_cantidad($value);
    }else if($name == "P12"){
        $nameR = "question6";
        $valueR = pregunta_cantidad($value);
    }else if($name == "P16"){
        $nameR = "question7";
        $valueR = pregunta_cantidad($value);
    }else if($name == "P18"){
        $nameR = "question8";
        $valueR = pregunta_cantidad($value);
    }else if($name == "P_edad"){
        $nameR = "question9";
        $valueR = $value;
    }else if($name == "Peducativo"){
        $nameR = "question10";
        $valueR = pregunta_educativo($value);
    }else if($name == "Continuacion_P19" || $name == "seguimiento_P19"){
        $nameR = "question11";
        $valueR = pregunta_cantidad2($value);
    }else if($name == "P20"){
        $nameR = "question12";
        $valueR = pregunta_cantidad2($value);
    }else if($name == "P22"){
        $nameR = "question13";
        $valueR = pregunta_calidad($value);
    }else if($name == "P25"){
        $nameR = "question14";
        $valueR = pregunta_si_no($value);
    }else if($name == "P29"){
        $nameR = "question15";
        $valueR = pregunta_cantidad2($value);
    }else if($name == "P30"){
        $nameR = "question16";
        $valueR = pregunta_cantidad($value);
    }else if($name == "P31"){
        $nameR = "question17";
        $valueR = pregunta_si_no($value);
    }else if($name == "P32"){
        $nameR = "question18";
        $valueR = pregunta_si_no($value);
    }else if($name == "P33"){
        $nameR = "question19";
        $valueR = pregunta_si_no($value);
    }else if($name == "P36"){
        $nameR = "question20";
        $valueR = pregunta_si_no($value);
    }else if($name == "P36_R_Cierre"){
        $nameR = "question21";
        $valueR = pregunta_si_no($value);
    }else{
        $nameR = "N/A";
        $valueR = "N/A";
    }
    return [$nameR, $valueR];
}

function pregunta_cantidad($value){
    $valor = $value;
    if($valor == 0){
        $response = "0 veces al mes";
    }else if($valor > 0 && $valor <= 15){
        $response = "1-15 veces al mes";
    }else if($valor > 15 && $valor <= 25){
        $response = "16-25 veces al mes";
    }else if($valor > 25 && $valor <= 30){
        $response = "26-30 veces al mes";
    }else if($valor > 30){
        $response = "26-30 veces al mes";
    }else{
        $response = "0 veces al mes";
    }
    return $response;
}

function pregunta_educativo($value){
    $response3 = $value;
    //Comparar valores
    if($response3 == 'Primaria'){
        $response4 = "Primaria";
    }else if($response3 == "Secundaria (o Bachillerato)"){
        $response4 = "Secundaria (o Bachillerato)";
    }else if($response3 == 'Formación profesional (Técnica)'){
        $response4 = "Formación profesional (Técnica)";
    }else if($response3 == 'Tecnólogo'){
        $response4 = "Tecnólogo";
    }else if($response3 == 'Profesional'){
        $response4 = "Profesional";
    }else if($response3 == 'Posgrado'){
        $response4 = "Posgrado";
    }else{
        $response4 = "Bachillerato";
    }
    return $response4;
}

function pregunta_cantidad2($value){
    $response2 = $value;
    if($response2 == 'cero' || $response2 == 0 || $response2 == 'zero'){
        $response3 = "CERO";
    }else if($response2 == 'uno' || $response2 == 1 || $response2 == 'one'){
        $response3 = "UNO";
    }else if($response2 == 'dos' || $response2 == 2 || $response2 == 'two'){
        $response3 = "DOS";
    }else if($response2 == 'tres' || $response2 == 3 || $response2 == 'three'){
        $response3 = "TRES";
    }else if($response2 == 'cuatro' || $response2 == 4|| $response2 == 'four'){
        $response3 = "CUATRO";
    }else if($response2 == 'cinco' || $response2 == 5 || $response2 == 'five'){
        $response3 = "CINCO";
    }else if($response2 == 'seis' || $response2 == 6 || $response2 == 'six'){
        $response3 = "6 o más";
    }else if($response2 == '6omas' || $response2 > 6 || $response2 == '6ormore'){
        $response3 = "6 o más";
    }else{
        $response3 = "CERO";
    }
    return $response3;
    
}

function pregunta_calidad($value){
    $response2 = $value;
    if($response2 == 'Muy mala' || $response2 == 'Muy mala'){
        $response3 = "Muy mala";
    }else if($response2 == 'Mala' || $response2 == 'Mala'){
        $response3 = "Mala";
    }else if($response2 == 'Regular' || $response2 == 'Regular'){
        $response3 = "Regular";
    }else if($response2 == 'Buena' || $response2 == 'Buena'){
        $response3 = "Buena";
    }else if($response2 == 'Muy buena' || $response2 == 'Muy buena'){
        $response3 = "Muy buena";
    }else if($response2 == 'No tengo una relación sentimental' || $response2 == 'No tengo una relación sentimental'){
        $response3 = "No tengo una relación sentimental";
    }else{
        //Si ninguna se acopla por defecto pone buena.
        $response3 = "Buena";
    }
    return $response3;
}

function pregunta_si_no($value){
    $response2 = $value;
    if($response2 == 'Si' || $response2 == 'YES'){
        $response3 = "Si";
    }else if($response2 == 'No' || $response2 == 'NOT'){
        $response3 = "No";
    }else{
        $response3 = "No";
    }
    return $response3;
}

function eliminar_acentos($cadena){
    //Reemplazamos la A y a
    $cadena = str_replace(
    array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
    array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
    $cadena
    );

    //Reemplazamos la E y e
    $cadena = str_replace(
    array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
    array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
    $cadena );

    //Reemplazamos la I y i
    $cadena = str_replace(
    array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
    array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
    $cadena );

    //Reemplazamos la O y o
    $cadena = str_replace(
    array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
    array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
    $cadena );

    //Reemplazamos la U y u
    $cadena = str_replace(
    array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
    array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
    $cadena );

    //Reemplazamos la N, n, C y c
    $cadena = str_replace(
    array('Ñ', 'ñ', 'Ç', 'ç'),
    array('N', 'n', 'C', 'c'),
    $cadena
    );
    
    return $cadena;
}

function salud_mental_individuo($user){
    //¿Durante el último mes cuántas veces has tenido poco interés o placer en hacer las cosas ?
    //$P1='';

    //¿Durante el último mes cuántas veces te has sentido decaído(a), deprimido(a) o sin esperanzas?
    //$P2='';

    //¿Durante el último mes cuántas veces has tenido cansancio extremo o poca energía?
    //$P3='';

    //¿Durante el último mes cuántas veces has tenido dificultad en conciliar el sueño o has dormido en exceso.?
    //$P4='';
    
    //¿Durante el último mes cuántas veces te has sentido mal contigo mismo(a) que eres un fracaso(a) o que has quedado mal con tu familia?
    //$P7='';

    //¿Durante el último mes cuántas veces has tenido tendencia a beber o fumar más de lo habitual?
    //$P9='';

    //¿Durante el último mes cuántas veces has sentido pinchazos o sensaciones dolorosas en distintas partes del cuerpo?
    //$P11='';

    //¿Durante el último mes cuántas veces has tenido temblores musculares (por ejemplo, tics nerviosos o parpadeos)?
    //$P12='';

    //Edad 
    //$P16='';

    //¿Cual es tu nivel educativo?
    //$P18='';

    //¿Con cuántas personas convives actualmente?
    //$P19='';

    //¿Cuántas personas tienes a cargo?
    //$P20='';

    //¿Tienes algún familiar que haya sufrido de algún trastorno mental?
    //$P25='';
    
    //¿Sufres de alguna enfermedad crónica que te cause dolor o incapacidad?
    //$P26='';

    //¿Cuántas veces consumes alcohol en un mes?
    //$P29='';

    //¿Has recibido tratamiento psicológico?
    //$P31='';

    //¿Has tenido tratamiento psiquiátrico?
    //$P32='';
    
    //¿Has atentado físicamente contra tu cuerpo?
    //$P36='';

    $test = TestmedbyteResult::select('*')
    ->where('user_id', $user)
    ->orderBy('created_at', 'desc')
    ->first();

    $FormularioRespuestas = json_decode($test->respuestas, true);

    //Validar que la pregunta de la edad ya esté registrada en el onboarding.
    $edad = validarPreguntaEdadOnboarding($user);
    //Si trae la edad, la junta a las respuestas, sino retorna un error
    if ($edad !== null) {
        $edad_final = [
            'pregunta' => [
                'id' => 9,
                'variable' => "P_edad",
                'pregunta' => "question9",
                'texto' => "¿Cuántos años tienes?"
            ],
            'respuesta' => [
                'texto' => $edad,
            ]
        ];
    } else {
        return response()->json([
            'success' => false,
            'message' => 'El usuario no ha realizado el onboarding.',
            'error_code' => 'ONBOARDING_NO_ENCONTRADO'
        ], 404);
    }
    $resultados_test = depurador_test($FormularioRespuestas);
    //Le añade la respuesta al final.
    $resultados_test[] = $edad_final; 
    $formulario_analisis = procesarRespuestas($resultados_test);
    //Le añade la respuesta al final.
    $resultados_test[] = $edad_final; 
    $formulario_analisis = procesarRespuestas($resultados_test);
    //Cargar valores de la encuesta que respondio el paciente.
    // 0 veces al mes , 1-15 veces al mes , 16-25 veces al mes ,26-30 veces al mes
    /*
    $P1='0 veces al mes'; 
    $P2='0 veces al mes'; 
    $P3='0 veces al mes'; 
    $P4='26-30 veces al mes'; 
    $P7='0 veces al mes'; 
    $P9='1-15 veces al mes'; 
    $P11='16-25 veces al mes'; 
    $P12='26-30 veces al mes'; 
    */
    $P1= $formulario_analisis['question1']['respuesta'] ?? null;
    $P2= $formulario_analisis['question2']['respuesta'] ?? null;
    $P3= $formulario_analisis['question3']['respuesta'] ?? null;
    $P4= $formulario_analisis['question4']['respuesta'] ?? null;
    $P7= $formulario_analisis['question5']['respuesta'] ?? null;
    $P9= $formulario_analisis['question6']['respuesta'] ?? null;
    $P11= $formulario_analisis['question7']['respuesta'] ?? null;
    $P12= $formulario_analisis['question8']['respuesta'] ?? null;
    // CERO , UNO , DOS  TRES , CUATRO , CINCO , 6 o más 
    /*
    $P19='CERO'; 
    $P20='UNO'; 
    $P29='6 o más'; 
    */
    $P19= $formulario_analisis['question11']['respuesta'] ?? null;
    $P20= $formulario_analisis['question12']['respuesta'] ?? null;
    $P29= $formulario_analisis['question15']['respuesta'] ?? null;
    // No , Si
    /*
    $P25='NO'; 
    $P26='NO'; 
    $P31='NO'; 
    $P32='NO'; 
    $P36='NO'; 
    */
    $P25= $formulario_analisis['question14']['respuesta'] ?? null;
    $P26= $formulario_analisis['question21']['respuesta'] ?? null;
    $P31= $formulario_analisis['question17']['respuesta'] ?? null;
    $P32= $formulario_analisis['question18']['respuesta'] ?? null;
    $P36= $formulario_analisis['question20']['respuesta'] ?? null;
    // Primaria , Bachillerato , Técnico/Tecnológico , Profesional , Posgrado
    //$P18='Bachillerato'; 
    $P18= $formulario_analisis['question10']['respuesta'] ?? null;
    // Entero
    //$P16=18;
    $P16= $formulario_analisis['question9']['respuesta'] ?? null;
    // ¿Cómo ves para ti esta relación sentimental?
    // Muy Buena , Buena , Regular , No tengo una relación sentimental , Mala , Muy mala
    //$P22 = 'No tengo una relación sentimental';
    $P22= $formulario_analisis['question13']['respuesta'] ?? null;
    //¿Tienes alguna enfermedad cardíaca?
    //$P33 = 'SI';
    $P33= $formulario_analisis['question19']['respuesta'] ?? null;

    list ( $scoreAnsiedad , $nivelRiesgoAnsiedad , $P2_WOE_Anxiety , $P4_WOE_Anxiety , $P7_WOE_Anxiety , $P9_WOE_Anxiety , $P11_WOE_Anxiety , $P12_WOE_Anxiety ,
    $P16_WOE_Anxiety , $P18_WOE_Anxiety , $P19_WOE_Anxiety , $P29_WOE_Anxiety , $P31_WOE_Anxiety , $P32_WOE_Anxiety  ) = scoreAnsiedadFI ($P2 , $P4 , $P7 , $P9 , $P11 , $P12 , $P16 , $P18 , $P19 , $P29 , $P31 , $P32 ); 

    $ResultadoAnsiedad = array(
        "scoreAnsiedad" => round($scoreAnsiedad,1),
        "nivelRiesgoAnsiedad" => $nivelRiesgoAnsiedad,
        "P2_WOE_Anxiety" => $P2_WOE_Anxiety,
        "P4_WOE_Anxiety" => $P4_WOE_Anxiety,
        "P7_WOE_Anxiety" => $P7_WOE_Anxiety,
        "P9_WOE_Anxiety" => $P9_WOE_Anxiety,
        "P11_WOE_Anxiety" => $P11_WOE_Anxiety,
        "P12_WOE_Anxiety" => $P12_WOE_Anxiety,
        "P16_WOE_Anxiety" => $P16_WOE_Anxiety,
        "P18_WOE_Anxiety" => $P18_WOE_Anxiety,
        "P19_WOE_Anxiety" => $P19_WOE_Anxiety,
        "P29_WOE_Anxiety" => $P29_WOE_Anxiety,
        "P31_WOE_Anxiety" => $P31_WOE_Anxiety,
        "P32_WOE_Anxiety" => $P32_WOE_Anxiety
    );

    list ( $scoreStress , $nivelRiesgoStress , $P1_WOE_Stress , $P3_WOE_Stress , $P4_WOE_Stress , $P12_WOE_Stress , $P16_WOE_Stress , $P20_WOE_Stress ,
    $P25_WOE_Stress , $P32_WOE_Stress , $P36_WOE_Stress , $P26_WOE_Stress ) = scoreStressFI ($P1 , $P3 , $P4 , $P12 , $P16 , $P20 , $P25 , $P26 , $P32 , $P36 ); 
    $ResultadoEstres = array(
        "scoreStress" => round($scoreStress,1),
        "nivelRiesgoStress" => $nivelRiesgoStress,
        "P1_WOE_Stress" => $P1_WOE_Stress,
        "P3_WOE_Stress" => $P3_WOE_Stress,
        "P4_WOE_Stress" => $P4_WOE_Stress,
        "P12_WOE_Stress" => $P12_WOE_Stress,
        "P16_WOE_Stress" => $P16_WOE_Stress,
        "P20_WOE_Stress" => $P20_WOE_Stress,
        "P25_WOE_Stress" => $P25_WOE_Stress,
        "P32_WOE_Stress" => $P32_WOE_Stress,
        "P36_WOE_Stress" => $P36_WOE_Stress,
        "P26_WOE_Stress" => $P26_WOE_Stress
    );
    $scoreTotal = 0.5 * $scoreAnsiedad + 0.5 * $scoreStress;
    if( $scoreTotal >=0 && $scoreTotal <27.5 ){ $nivelRiesgoTotal='Muy Alto';}
    if( $scoreTotal >=27.5 && $scoreTotal <57 ){ $nivelRiesgoTotal='Alto';}
    if( $scoreTotal >=57 && $scoreTotal <72.6 ){ $nivelRiesgoTotal='Medio';}
    if( $scoreTotal >=72.6 && $scoreTotal <79.9 ){ $nivelRiesgoTotal='Bajo';}
    if( $scoreTotal >=79.9 && $scoreTotal <85.7 ){ $nivelRiesgoTotal='Bajo';}
    if( $scoreTotal >=85.7 && $scoreTotal <89.6 ){ $nivelRiesgoTotal='Bajo';}
    if( $scoreTotal >=89.6 && $scoreTotal <90.9 ){ $nivelRiesgoTotal='Bajo';}
    if( $scoreTotal >=90.9 && $scoreTotal <95.5 ){ $nivelRiesgoTotal='Bajo';}
    if( $scoreTotal >=95.5 && $scoreTotal <97.2 ){ $nivelRiesgoTotal='Muy Bajo';}
    if( $scoreTotal >=97.2 && $scoreTotal <100 ){ $nivelRiesgoTotal='Muy Bajo';}
    $ResultadoScoreTotal = array(
        "scoreTotal" => round($scoreTotal,1),
        "nivelRiesgoTotal" => $nivelRiesgoTotal
    );
    list ($msg1 , $msg2, $msg3, $msg4, $msg5) = mensajesScoreTotalFI ($P2 , $P4 , $P7 , $P9 , $P12 , $P19 , $P20 , $P22 , $P29 , $P31 , $P32 , $P33 , $P36);
    $ResultadoMensajes = array(
        "mensaje_1" => $msg1,
        "mensaje_2" => $msg2,
        "mensaje_3" => $msg3,
        "mensaje_4" => $msg4,
        "mensaje_5" => $msg5
    );
    $P1_Total_WOE = $P1_WOE_Stress * 0.5;
    $P2_Total_WOE = $P2_WOE_Anxiety * 0.5;
    $P3_Total_WOE = $P3_WOE_Stress * 0.5;
    $P4_Total_WOE = $P4_WOE_Anxiety * 0.5 + $P4_WOE_Stress * 0.5;
    $P7_Total_WOE = $P7_WOE_Anxiety * 0.5;
    $P9_Total_WOE = $P9_WOE_Anxiety * 0.5;
    $P11_Total_WOE = $P11_WOE_Anxiety * 0.5;
    $P12_Total_WOE = $P12_WOE_Anxiety * 0.5 + $P12_WOE_Stress * 0.5;
    $P16_Total_WOE = $P16_WOE_Anxiety * 0.5 + $P16_WOE_Stress * 0.5;
    $P18_Total_WOE = $P18_WOE_Anxiety * 0.5;
    $P19_Total_WOE = $P19_WOE_Anxiety * 0.5;
    $P20_Total_WOE = $P20_WOE_Stress * 0.5;
    $P25_Total_WOE = $P25_WOE_Stress * 0.5;
    $P29_Total_WOE = $P29_WOE_Anxiety * 0.5;
    $P31_Total_WOE = $P31_WOE_Anxiety * 0.5;
    $P32_Total_WOE = $P32_WOE_Anxiety * 0.5 + $P32_WOE_Stress * 0.5;
    $P36_Total_WOE = $P36_WOE_Stress * 0.5;
    $P26_Total_WOE = $P26_WOE_Stress * 0.5;

    $Total_WOE = $P1_WOE_Stress * 0.5 + $P2_WOE_Anxiety * 0.5 + $P3_WOE_Stress * 0.5 + $P4_WOE_Anxiety * 0.5 + $P4_WOE_Stress * 0.5 + $P7_WOE_Anxiety * 0.5 
    + $P9_WOE_Anxiety * 0.5 + $P11_WOE_Anxiety * 0.5 + $P12_WOE_Anxiety * 0.5 + $P12_WOE_Stress * 0.5 + $P16_WOE_Anxiety * 0.5 + $P16_WOE_Stress * 0.5 + $P18_WOE_Anxiety * 0.5 
    + $P19_WOE_Anxiety * 0.5 + $P20_WOE_Stress * 0.5 + $P25_WOE_Stress * 0.5 + $P29_WOE_Anxiety * 0.5 + $P31_WOE_Anxiety * 0.5 + $P32_WOE_Anxiety * 0.5 + $P32_WOE_Stress * 0.5 
    + $P36_WOE_Stress * 0.5 + $P26_WOE_Stress * 0.5;

    $P1_Total_WOE = $P1_WOE_Stress * 0.5 / $Total_WOE;
    $P2_Total_WOE = $P2_WOE_Anxiety * 0.5 / $Total_WOE;
    $P3_Total_WOE = $P3_WOE_Stress * 0.5 / $Total_WOE;
    $P4_Total_WOE = ($P4_WOE_Anxiety * 0.5 + $P4_WOE_Stress * 0.5) / $Total_WOE;
    $P7_Total_WOE = $P7_WOE_Anxiety * 0.5 / $Total_WOE;
    $P9_Total_WOE = $P9_WOE_Anxiety * 0.5 / $Total_WOE;
    $P11_Total_WOE = $P11_WOE_Anxiety * 0.5 / $Total_WOE;
    $P12_Total_WOE = ($P12_WOE_Anxiety * 0.5 + $P12_WOE_Stress * 0.5) / $Total_WOE;
    $P16_Total_WOE = ($P16_WOE_Anxiety * 0.5 + $P16_WOE_Stress * 0.5) / $Total_WOE;
    $P18_Total_WOE = $P18_WOE_Anxiety * 0.5 / $Total_WOE;
    $P19_Total_WOE = $P19_WOE_Anxiety * 0.5 / $Total_WOE;
    $P20_Total_WOE = $P20_WOE_Stress * 0.5 / $Total_WOE;
    $P25_Total_WOE = $P25_WOE_Stress * 0.5 / $Total_WOE;
    $P29_Total_WOE = $P29_WOE_Anxiety * 0.5 / $Total_WOE;
    $P31_Total_WOE = $P31_WOE_Anxiety * 0.5 / $Total_WOE;
    $P32_Total_WOE = ($P32_WOE_Anxiety * 0.5 + $P32_WOE_Stress * 0.5) / $Total_WOE;
    $P36_Total_WOE = $P36_WOE_Stress * 0.5 / $Total_WOE;
    $P26_Total_WOE = $P26_WOE_Stress * 0.5 / $Total_WOE;

    $P1_Total_WOE = $P1_WOE_Stress * 0.5 / $Total_WOE;
    $P2_Total_WOE = $P2_WOE_Anxiety * 0.5 / $Total_WOE;
    $P3_Total_WOE = $P3_WOE_Stress * 0.5 / $Total_WOE;
    $P4_Total_WOE = ($P4_WOE_Anxiety * 0.5 + $P4_WOE_Stress * 0.5) / $Total_WOE;
    $P7_Total_WOE = $P7_WOE_Anxiety * 0.5 / $Total_WOE;
    $P9_Total_WOE = $P9_WOE_Anxiety * 0.5 / $Total_WOE;
    $P11_Total_WOE = $P11_WOE_Anxiety * 0.5 / $Total_WOE;
    $P12_Total_WOE = ($P12_WOE_Anxiety * 0.5 + $P12_WOE_Stress * 0.5) / $Total_WOE;
    $P16_Total_WOE = ($P16_WOE_Anxiety * 0.5 + $P16_WOE_Stress * 0.5) / $Total_WOE;
    $P18_Total_WOE = $P18_WOE_Anxiety * 0.5 / $Total_WOE;
    $P19_Total_WOE = $P19_WOE_Anxiety * 0.5 / $Total_WOE;
    $P20_Total_WOE = $P20_WOE_Stress * 0.5 / $Total_WOE;
    $P25_Total_WOE = $P25_WOE_Stress * 0.5 / $Total_WOE;
    $P29_Total_WOE = $P29_WOE_Anxiety * 0.5 / $Total_WOE;
    $P31_Total_WOE = $P31_WOE_Anxiety * 0.5 / $Total_WOE;
    $P32_Total_WOE = ($P32_WOE_Anxiety * 0.5 + $P32_WOE_Stress * 0.5) / $Total_WOE;
    $P36_Total_WOE = $P36_WOE_Stress * 0.5 / $Total_WOE;
    $P26_Total_WOE = $P26_WOE_Stress * 0.5 / $Total_WOE;

    $ResultadoScoreTotalFinal = array(
        "P1_Total_WOE" => $P1_Total_WOE,
        "P2_Total_WOE" => $P2_Total_WOE,
        "P3_Total_WOE" => $P3_Total_WOE,
        "P4_Total_WOE" => $P4_Total_WOE,
        "P7_Total_WOE" => $P7_Total_WOE,
        "P9_Total_WOE" => $P9_Total_WOE,
        "P11_Total_WOE" => $P11_Total_WOE,
        "P12_Total_WOE" => $P12_Total_WOE,
        "P16_Total_WOE" => $P16_Total_WOE,
        "P18_Total_WOE" => $P18_Total_WOE,
        "P19_Total_WOE" => $P19_Total_WOE,
        "P20_Total_WOE" => $P20_Total_WOE,
        "P25_Total_WOE" => $P25_Total_WOE,
        "P29_Total_WOE" => $P29_Total_WOE,
        "P31_Total_WOE" => $P31_Total_WOE,
        "P32_Total_WOE" => $P32_Total_WOE,
        "P36_Total_WOE" => $P36_Total_WOE,
        "P26_Total_WOE" => $P26_Total_WOE
    );

    $resultado[0] = $ResultadoEstres;
    $resultado[1] = $ResultadoAnsiedad;
    $resultado[2] = $ResultadoScoreTotal;
    $resultado[3] = $ResultadoMensajes;
    $resultado[4] = $ResultadoScoreTotalFinal;

    return $resultado;

}

//Funciones para el factor de importancia.
function scoreAnsiedadFI ($P2 , $P4 , $P7 , $P9 , $P11 , $P12 , $P16 , $P18 , $P19 , $P29 , $P31 , $P32 ){

    $fa = 0;
    $pa = 0;
    $nivelRiesgoAnsiedad = '';

    //$P2_WOE  = 0; 
    //$P4_WOE  = 0;
    //$P7_WOE  = 0;
    //$P9_WOE  = 0;
    //$P11_WOE  = 0;
    //$P12_WOE  = 0; 
    //$P16_WOE  = 0;
    //$P18_WOE  = 0;
    //$P19_WOE  = 0;
    //$P29_WOE  = 0;
    //$P31_WOE  = 0;
    //$P32_WOE  = 0; 

    //$P2_WoE_Anxiety = 0;
    //$P4_WoE_Anxiety = 0;
    //$P7_WoE_Anxiety = 0;
    //$P9_WoE_Anxiety = 0;
    //$P11_WoE_Anxiety = 0;
    //$P12_WoE_Anxiety = 0;
    //$P16_WoE_Anxiety = 0;
    //$P18_WoE_Anxiety = 0;
    //$P19_WoE_Anxiety = 0;
    //$P29_WoE_Anxiety = 0;
    //$P31_WoE_Anxiety = 0;
    //$P32_WoE_Anxiety = 0;

    //2 - ¿Durante el último mes cuántas veces te has sentido decaído(a), deprimido(a) o sin esperanzas?
    if($P2=='0 veces al mes'){ $P2_WOE = 1.685043;}
    if($P2=='1-15 veces al mes'){ $P2_WOE = 0.119869;}
    if($P2=='16-25 veces al mes'){ $P2_WOE = -1.316009;}
    if($P2=='26-30 veces al mes'){ $P2_WOE = -3.222698;}

    //4 - ¿Durante el último mes cuántas veces has tenido dificultad en conciliar el sueño o has dormido en exceso.?
    if($P4=='0 veces al mes'){ $P4_WOE = 1.2687431;}
    if($P4=='1-15 veces al mes'){ $P4_WOE = 0.1039352;}
    if($P4=='16-25 veces al mes'){ $P4_WOE = -0.6890015;}
    if($P4=='26-30 veces al mes'){ $P4_WOE = -1.3255783;}

    //7 - ¿Durante el último mes cuántas veces te has sentido mal contigo mismo(a) – que eres un fracaso(a) o que has quedado mal con tu familia?
    if($P7=='0 veces al mes'){ $P7_WOE = 1.43730437;}
    if($P7=='1-15 veces al mes'){ $P7_WOE = -0.08922837;}
    if($P7=='16-25 veces al mes'){ $P7_WOE = -1.23596615;}
    if($P7=='26-30 veces al mes'){ $P7_WOE = -2.43993896;}

    //9 - ¿Durante el último mes cuántas veces has tenido tendencia a beber o fumar más de lo habitual?
    if($P9=='0 veces al mes'){ $P9_WOE = 0.3641484;}
    if($P9=='1-15 veces al mes'){ $P9_WOE = -0.1018029;}
    if($P9=='16-25 veces al mes'){ $P9_WOE = -1.2767881;}
    if($P9=='26-30 veces al mes'){ $P9_WOE = -2.8862261;}

    //11 - ¿Durante el último mes cuántas veces has sentido pinchazos o sensaciones dolorosas en distintas partes del cuerpo?
    if($P11=='0 veces al mes'){ $P11_WOE = 0.863278;}
    if($P11=='1-15 veces al mes'){ $P11_WOE = -0.4294903;}
    if($P11=='16-25 veces al mes'){ $P11_WOE = -2.1034667;}
    if($P11=='26-30 veces al mes'){ $P11_WOE = -1.8364039;}

    //12 - ¿Durante el último mes cuántas veces has tenido temblores musculares (por ejemplo, tics nerviosos o parpadeos)?
    if($P12=='0 veces al mes'){ $P12_WOE = 0.9490098;}
    if($P12=='1-15 veces al mes'){ $P12_WOE = -0.4918334;}
    if($P12=='16-25 veces al mes'){ $P12_WOE = -1.8958274;}
    if($P12=='26-30 veces al mes'){ $P12_WOE = -1.9699353;}

    //16 - Edad
    if($P16<15){ $P16 = 18;}
    if( $P16>=15 && $P16 <=21 ){ $P16_WOE=-0.22073547;}
    if( $P16>=22 && $P16 <=23 ){ $P16_WOE=-0.11363734;}
    if( $P16>=24 && $P16 <=25 ){ $P16_WOE=-0.48833079;}
    if( $P16>=26 && $P16 <=26 ){ $P16_WOE=-0.46585793;}
    if( $P16>=27 && $P16 <=28 ){ $P16_WOE=-0.11363734;}
    if( $P16>=29 && $P16 <=31 ){ $P16_WOE=0.49087377;}
    if( $P16>=32 && $P16 <=33 ){ $P16_WOE=0.70421332;}
    if( $P16>=34 && $P16 <=39 ){ $P16_WOE=-0.15285805;}
    if( $P16>=40 && $P16 <=47 ){ $P16_WOE=-0.04002552;}
    if( $P16>=48 && $P16 <=68 ){ $P16_WOE=0.85144356;}

    //18 - ¿Cual es tu nivel educativo?
    if($P18=='Primaria'){ $P18_WOE = 0;}
    if($P18=='Bachillerato'){ $P18_WOE = -0.2522838;}
    if($P18=='Técnico'){ $P18_WOE = 0.1445975;}
    if($P18=='Tecnológico'){ $P18_WOE = 0.1445975;}
    if($P18=='Profesional'){ $P18_WOE = 0.1388338;}
    if($P18=='Posgrado'){ $P18_WOE = -0.3117073;}
    else{
        $P18_WOE = 0;
    }

    //19 - ¿Con cuántas personas convives actualmente?
    if($P19=='CERO'){ $P19_WOE = 0.10950621;}
    if($P19=='UNO'){ $P19_WOE = -0.09116448;}
    if($P19=='DOS'){ $P19_WOE = 0.10053754;}
    if($P19=='TRES'){ $P19_WOE = -0.3692311;}
    if($P19=='CUATRO'){ $P19_WOE = -0.17817586;}
    if($P19=='CINCO'){ $P19_WOE = 0;}
    if($P19=='6 o más'){ $P19_WOE = 1.36226918;}

    //29 - ¿Cuántas veces consumes alcohol en un mes?
    if($P29=='CERO'){ $P29_WOE = 0.1746455;}
    if($P29=='UNO'){ $P29_WOE = -0.1018029;}
    if($P29=='DOS'){ $P29_WOE = 0.5315006;}
    if($P29=='TRES'){ $P29_WOE = 0.6059431;}
    if($P29=='CUATRO'){ $P29_WOE = -0.7171724;}
    if($P29=='CINCO'){ $P29_WOE = -0.4013194;}
    if($P29=='6 o más'){ $P29_WOE = -0.9513657;}

    //31 - ¿Has recibido tratamiento psicológico?
    if($P31=='No'){ $P31_WOE = 0.7863929;}
    if($P31=='Si'){ $P31_WOE = -0.8873234;}

    //32 - ¿Has tenido tratamiento psiquiátrico?
    if($P32=='No'){ $P32_WOE = 0.4878203;}
    if($P32=='Si'){ $P32_WOE = -2.6117892;}

    // Weight by variable Anxiety

    $P2_WOE_Anxiety = 1 / (1 + exp (-($P2_WOE * 0.395 + 0.9864)));
    $P4_WOE_Anxiety = 1 / (1 + exp (-($P4_WOE * 0.46 + 0.9864)));
    $P7_WOE_Anxiety = 1 / (1 + exp (-($P7_WOE * 0.4786 + 0.9864)));
    $P9_WOE_Anxiety = 1 / (1 + exp (-($P9_WOE * 0.6469 + 0.9864)));
    $P11_WOE_Anxiety = 1 / (1 + exp (-($P11_WOE * 0.7129 + 0.9864)));
    $P12_WOE_Anxiety = 1 / (1 + exp (-($P12_WOE * 0.3762 + 0.9864)));
    $P16_WOE_Anxiety = 1 / (1 + exp (-($P16_WOE * 1.3969 + 0.9864)));
    $P18_WOE_Anxiety = 1 / (1 + exp (-($P18_WOE * 1.2349 + 0.9864)));
    $P19_WOE_Anxiety = 1 / (1 + exp (-($P19_WOE * -2.4087 + 0.9864)));
    $P29_WOE_Anxiety = 1 / (1 + exp (-($P29_WOE * 0.5845 + 0.9864)));
    $P31_WOE_Anxiety = 1 / (1 + exp (-($P31_WOE * 0.9462 + 0.9864)));
    $P32_WOE_Anxiety = 1 / (1 + exp (-($P32_WOE * 1.422 + 0.9864)));

    $fa =  $P2_WOE * 0.395 
            + $P4_WOE * 0.46 
            + $P7_WOE * 0.4786 
            + $P9_WOE * 0.6469 
            + $P11_WOE * 0.7129 
            + $P12_WOE * 0.3762 
            + $P16_WOE * 1.3969 
            + $P18_WOE * 1.2349 
            + $P19_WOE * -2.4087 
            + $P29_WOE * 0.5845 
            + $P31_WOE * 0.9462 
            + $P32_WOE * 1.422 
            + 0.9864;

    $pa = 1 / (1 +  exp(-$fa));

    $scoreAnsiedad = $pa * 100;

    if( $scoreAnsiedad >=0 && $scoreAnsiedad <17.508 ){ $nivelRiesgoAnsiedad ='Muy Alto';}
    if( $scoreAnsiedad >=17.508 && $scoreAnsiedad <57.825 ){ $nivelRiesgoAnsiedad ='Alto';}
    if( $scoreAnsiedad >=57.825 && $scoreAnsiedad <78.577 ){ $nivelRiesgoAnsiedad ='Medio';}
    if( $scoreAnsiedad >=78.577 && $scoreAnsiedad <88.886 ){ $nivelRiesgoAnsiedad ='Bajo';}
    if( $scoreAnsiedad >=88.886 && $scoreAnsiedad <93.661 ){ $nivelRiesgoAnsiedad ='Bajo';}
    if( $scoreAnsiedad >=93.661 && $scoreAnsiedad <96.831 ){ $nivelRiesgoAnsiedad ='Bajo';}
    if( $scoreAnsiedad >=96.831 && $scoreAnsiedad <98.334 ){ $nivelRiesgoAnsiedad ='Bajo';}
    if( $scoreAnsiedad >=98.334 && $scoreAnsiedad <98.973 ){ $nivelRiesgoAnsiedad ='Bajo';}
    if( $scoreAnsiedad >=98.973 && $scoreAnsiedad <99.428 ){ $nivelRiesgoAnsiedad ='Muy Bajo';}
    if( $scoreAnsiedad >=99.428 && $scoreAnsiedad <100 ){ $nivelRiesgoAnsiedad ='Muy Bajo';}

    return array ($scoreAnsiedad , $nivelRiesgoAnsiedad , $P2_WOE_Anxiety , $P4_WOE_Anxiety , $P7_WOE_Anxiety , $P9_WOE_Anxiety , $P11_WOE_Anxiety , $P12_WOE_Anxiety ,
    $P16_WOE_Anxiety , $P18_WOE_Anxiety , $P19_WOE_Anxiety , $P29_WOE_Anxiety , $P31_WOE_Anxiety , $P32_WOE_Anxiety );
}

function scoreStressFI ($P1 , $P3 , $P4 , $P12 , $P16 , $P20 , $P25 , $P26 , $P32 , $P36){    
    $fs = 0;
    $ps = 0;
    $nivelRiesgoStress = '';

    //$P1_WOE = 0;
    //$P3_WOE  = 0;
    //$P4_WOE  = 0;
    //$P12_WOE  = 0;
    //$P16_WOE  = 0;
    //$P20_WOE  = 0;
    //$P25_WOE  = 0;
    //$P26_WOE  = 0;
    //$P32_WOE  = 0;
    //$P36_WOE  = 0;

    //$P1_WoE_Stress = 0;
    //$P3_WoE_Stress = 0;
    //$P4_WoE_Stress = 0;
    //$P12_WoE_Stress = 0;
    //$P16_WoE_Stress = 0;
    //$P20_WoE_Stress = 0;
    //$P25_WoE_Stress = 0;
    //$P32_WoE_Stress = 0;
    //$P36_WoE_Stress = 0;
    //$P26_WoE_Stress = 0;

    //1 - ¿Durante el último mes cuántas veces has tenido poco interés o placer en hacer las cosas ?
    if($P1=='0 veces al mes'){ $P1_WOE = 0.96132577;}
    if($P1=='1-15 veces al mes'){ $P1_WOE = 0.02178973;}
    if($P1=='16-25 veces al mes'){ $P1_WOE = -0.70898014;}
    if($P1=='26-30 veces al mes'){ $P1_WOE = -0.7176382;}

    //3 - ¿Durante el último mes cuántas veces has tenido cansancio extremo o poca energía?
    if($P3=='0 veces al mes'){ $P3_WOE = 1.4105935;}
    if($P3=='1-15 veces al mes'){ $P3_WOE = 0.2942816;}
    if($P3=='16-25 veces al mes'){ $P3_WOE = -0.9934255;}
    if($P3=='26-30 veces al mes'){ $P3_WOE = -1.2461634;}

    //4 - ¿Durante el último mes cuántas veces has tenido dificultad en conciliar el sueño o has dormido en exceso.?
    if($P4=='0 veces al mes'){ $P4_WOE = 0.8299243;}
    if($P4=='1-15 veces al mes'){ $P4_WOE = 0.1341608;}
    if($P4=='16-25 veces al mes'){ $P4_WOE = -0.7976809;}
    if($P4=='26-30 veces al mes'){ $P4_WOE = -1.0873852;}

    //12 - ¿Durante el último mes cuántas veces has tenido temblores musculares (por ejemplo, tics nerviosos o parpadeos)?
    if($P12=='0 veces al mes'){ $P12_WOE = 0.5607083;}
    if($P12=='1-15 veces al mes'){ $P12_WOE = -0.3716872;}
    if($P12=='16-25 veces al mes'){ $P12_WOE = -1.1414524;}
    if($P12=='26-30 veces al mes'){ $P12_WOE = -2.1935447;}

    //16 - Edad
    if( $P16<15){ $P16 = 18;}
    if( $P16 >=15 && $P16 <=21 ){ $P16_WOE =0.7078769;}
    if( $P16 >=22 && $P16 <=23 ){ $P16_WOE =-0.2476346;}
    if( $P16 >=24 && $P16 <=25 ){ $P16_WOE =-0.4299561;}
    if( $P16 >=26 && $P16 <=26 ){ $P16_WOE =-0.1298515;}
    if( $P16 >=27 && $P16 <=28 ){ $P16_WOE =0.6125667;}
    if( $P16 >=29 && $P16 <=31 ){ $P16_WOE =-0.2476346;}
    if( $P16 >=32 && $P16 <=33 ){ $P16_WOE =-0.381166;}
    if( $P16 >=34 && $P16 <=39 ){ $P16_WOE =-0.1024526;}
    if( $P16 >=40 && $P16 <=47 ){ $P16_WOE =-0.2098942;}
    if( $P16 >=48 && $P16 <=68 ){ $P16_WOE =0.6433384;}

    //20 - ¿Cuántas personas tienes a cargo?
    if($P20=='CERO'){ $P20_WOE = 0.063578;}
    if($P20=='UNO'){ $P20_WOE = 0.4943028;}
    if($P20=='DOS'){ $P20_WOE = -0.6445159;}
    if($P20=='TRES'){ $P20_WOE = -0.7866311;}
    if($P20=='CUATRO'){ $P20_WOE = 1.0051284;}
    if($P20=='CINCO'){ $P20_WOE = 1.0051284;}
    if($P20=='6 o más'){ $P20_WOE = -1.6339289;}

    //25 - ¿Tienes algún familiar que haya sufrido de algún trastorno mental?
    if($P25=='No'){ $P25_WOE = 0.3168952;}
    if($P25=='Si'){ $P25_WOE = -0.4204774;}

    //26 - ¿Sufres de alguna enfermedad crónica que te cause dolor o incapacidad?
    if($P26=='No'){ $P26_WOE = 0.1200902;}
    if($P26=='Si'){ $P26_WOE = -0.8537704;}

    //32 - ¿Has tenido tratamiento psiquiátrico?
    if($P32=='No'){ $P32_WOE = 0.328845;}
    if($P32=='Si'){ $P32_WOE = -2.153804;}

    //36 - ¿Has atentado físicamente contra tu cuerpo?
    if($P36=='No'){ $P36_WOE = 0.05927886;}
    if($P36=='Si'){ $P36_WOE = -0.42995613;}

    // Weigth WoE Stress Isolate

    $P1_WOE_Stress = 1 / (1 + exp (-($P1_WOE * 0.4397 + 0.7967)));
    $P3_WOE_Stress = 1 / (1 + exp (-($P3_WOE * 0.7383 + 0.7967)));
    $P4_WOE_Stress = 1 / (1 + exp (-($P4_WOE * 0.4351 + 0.7967)));
    $P12_WOE_Stress = 1 / (1 + exp (-($P12_WOE * 0.6074 + 0.7967)));
    $P16_WOE_Stress = 1 / (1 + exp (-($P16_WOE * 1.4001 + 0.7967)));
    $P20_WOE_Stress = 1 / (1 + exp (-($P20_WOE * 0.9466 + 0.7967)));
    $P25_WOE_Stress = 1 / (1 + exp (-($P25_WOE * 0.6792 + 0.7967)));
    $P32_WOE_Stress = 1 / (1 + exp (-($P32_WOE * 1.1041 + 0.7967)));
    $P36_WOE_Stress = 1 / (1 + exp (-($P36_WOE * 2.4064 + 0.7967)));
    $P26_WOE_Stress = 1 / (1 + exp (-($P26_WOE * 0.762 + 0.7967)));

    $fs =  $P1_WOE * 0.4397
            + $P3_WOE * 0.7383
            + $P4_WOE * 0.4351
            + $P12_WOE * 0.6074
            + $P16_WOE * 1.4001
            + $P20_WOE * 0.9466
            + $P25_WOE * 0.6792
            + $P26_WOE * 0.762
            + $P32_WOE * 1.1041
            + $P36_WOE * 2.4064
            + 0.7967;
            
    $ps = 1 / (1 +  exp(-$fs));

    $scoreStress = $ps * 100;

    if( $scoreStress >=0 && $scoreStress <27 ){ $nivelRiesgoStress ='Muy Alto';}
    if( $scoreStress >=27 && $scoreStress <47.5 ){ $nivelRiesgoStress ='Alto';}
    if( $scoreStress >=47.5 && $scoreStress <65.3 ){ $nivelRiesgoStress ='Medio';}
    if( $scoreStress >=65.3 && $scoreStress <75 ){ $nivelRiesgoStress ='Bajo';}
    if( $scoreStress >=75 && $scoreStress <82.1 ){ $nivelRiesgoStress ='Bajo';}
    if( $scoreStress >=82.1 && $scoreStress <86.3 ){ $nivelRiesgoStress ='Bajo';}
    if( $scoreStress >=86.3 && $scoreStress <90.9 ){ $nivelRiesgoStress ='Bajo';}
    if( $scoreStress >=90.9 && $scoreStress <93.93 ){ $nivelRiesgoStress ='Bajo';}
    if( $scoreStress >=93.93 && $scoreStress <99.44 ){ $nivelRiesgoStress ='Muy Bajo';}
    if( $scoreStress >=99.44 && $scoreStress <100 ){ $nivelRiesgoStress ='Muy Bajo';}

    return array ($scoreStress , $nivelRiesgoStress , $P1_WOE_Stress , $P3_WOE_Stress , $P4_WOE_Stress , $P12_WOE_Stress , $P16_WOE_Stress , $P20_WOE_Stress ,
    $P25_WOE_Stress , $P32_WOE_Stress , $P36_WOE_Stress , $P26_WOE_Stress );

}

function mensajesScoreTotalFI ($P2 , $P4 , $P7 , $P9 , $P12 , $P19 , $P20 , $P22 , $P29 , $P31 , $P32 , $P33 , $P36){
    $msg1 = '';
    $msg2 = '';
    $msg3 = '';
    $msg4 = '';
    $msg5 = '';
        
    //P2    =IF(OR($C3="16-25 veces al mes",$C3= "26-30 veces al mes"),1,0)
    //P31    =IF($C19="Si",1,0)
    //P36    =IF($C22="Si",1,0)
    // Mensaje 1    
    if (($P2 == '16-25 veces al mes' || $P2== '26-30 veces al mes') && $P31=='SI' && $P36 =='SI'){
        $msg1 = 'Alerta: Riesgo de volver a tener autolesiones físicas. Acciones: 1) Intervenir preguntándo estado de ánimo, 2) Antes de 48 horas llamada telefonica del sicologo verificando estado de animo y 3) Monitoreo de evolución por una semana.';
    }  
    

    //P4    =IF(OR($C5="16-25 veces al mes",$C5= "26-30 veces al mes"),1,0)
    //P12    =IF(OR($C9="16-25 veces al mes",$C9= "26-30 veces al mes"),1,0)
    //P20    =IF(OR($C14="TRES",$C14= "CUATRO"),1,0)

    if(($P4 == '16-25 veces al mes' || $P4 == '26-30 veces al mes') && ($P12 =='16-25 veces al mes' || $P12 == '26-30 veces al mes') && ($P20 =='TRES' || $P20 == 'CUATRO' || $P20 =='CIMCO' || $P20 == '6 o más')){
        $msg2 = 'Alerta: Persona con riesgo de estrés Acción: 1. Intervenir brindándo apoyo, 2. Retomar conversación cada dos semanas.';
    }


    //P7    =IF(OR($C6="16-25 veces al mes",$C6= "26-30 veces al mes"),1,0)
    //P19    =IF($C13="CERO",1,0)
    //P20    =IF($C14="CERO",1,0)
    //P22    =IF($C15="No tengo una relación sentimental",1,0)

    if (($P7 == '16-25 veces al mes' || $P7 == '26-30 veces al mes') && $P19 =='CERO' && $P20 =='CERO' && $P22 =='No tengo una relación sentimental'){
        $msg3 = 'Alerta: Persona solitaria que puede carecer de red de apoyo y ser propenso a iniciar una depresión. Acción: Monitorear mínimo 1 vez al mes.';
    }


    //P4    =IF(OR($C5="16-25 veces al mes",$C5= "26-30 veces al mes"),1,0)
    //P9    =IF(OR($C7="16-25 veces al mes",$C7= "26-30 veces al mes"),1,0)
    //P22    =IF(C15="Muy Mala",1,0)

    if (($P4 == '6-25 veces al mes' || $P4 == '26-30 veces al mes') && ($P9 =='16-25 veces al mes' || $P9 == '26-30 veces al mes') && $P22 =='Muy Mala' ){
        $msg4 = 'Alerta: Principios de ansiedad Acciones: 1. Contacto de sicologo y hacer diagnóstico, indagando por síntomas ansiosos y recomendarle al paciente tips para la ansiedad, 2. Intervenir a la semana para verificar proceso';
    }


    //P29    =IF(OR($C17="TRES",$C17="CUATRO",$C17="CINCO",$C17="6 o más"),1,0)
    //P32    =IF($C20="Si",1,0)
    //P33    =IF($C21="Si",1,0)

    if (($P29 =='TRES' || $P29 =='CUATRO' || $P29 =='CINCO' || $P29 =='6 o más') && $P32 =='SI' && $P33 =='SI') {
        $msg5 = 'Alerta: Persona medicada con riesgo de variaciones o cambios emocionales bruscos. Acción: Intervenir por vía telefónica mínimo 2 veces al mes para monitorear riesgo.';
    }

    return array ($msg1 , $msg2, $msg3, $msg4, $msg5);

}