<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListadoMision;
use App\Models\ListadoSubmision;
use App\Models\Perfilacion;
use App\Models\PlanesMision;
use App\Models\PlanesPersonalizado;
use App\Models\PlanesCategoria;
use App\Models\PlanesSubmision;
use App\Models\Racha;
use Illuminate\Http\Request;
use App\Models\ScoreResult;
use App\Models\UserLogro;
use App\Models\UserPunto;
use App\Models\UserAsignacion;
use Carbon\Carbon;

class PlanPersonalizadoController extends Controller
{
    //Información que se le envía al CHAT GPT para generar el plan personalizado.
    public function info_user(){
        $user = auth()->id();
        $score = ScoreResult::where('score_results.user_id', '=', $user)
            ->join('nivel_riesgos', 'nivel_riesgos.id', '=', 'score_results.nivel_riesgo_id')
            ->select('score_results.*', 'nivel_riesgos.nombre AS nombre_nivel_riesgo')
            ->orderBy('score_results.id', 'desc') // Ordena por la columna 'id' en orden descendente
            ->get()
            ->makeHidden(['created_at', 'updated_at', 'id', 'user_id'])
            ->first(); // Obtiene el primer registro después de ordenar
        // Retornar una respuesta exitosa
        return response()->json($score, 200);
    }

    //Crear el plan personalizado desde cero.
    public function store(Request $request){
        //Selecciona el usuario
        $user = auth()->id();
        $fecha_actual = Carbon::now()->format('Y-m-d');
        //Validar que el usuario NO tenga un plan vigente.
        $planVigente = PlanesPersonalizado::where('user_id', $user)
            ->where('vigente', 1)
            ->exists();
        if ($planVigente) {
            return response()->json([
                'message' => 'Ya cuenta con un plan vigente, no se puede crear otro.'
            ], 400);
        //Capturar la respuesta de las 8 dimensiones de la persona interesada, del test de perfilamiento
        $perfilacion = Perfilacion::where('user_id', $user)->orderBy('id', 'desc')->first();
        //Validar antes que tenga perfilación
        if (is_null($perfilacion)) {
            return response()->json([
                'message' => 'El usuario no cuenta con perfilación, debe realizar el test primero.'
            ], 400);
        }
        $respuestas_perfilacion = json_decode($perfilacion->respuestas);
        //La pregunta 1, que es de slección multiple, es la que trae eesas respuestas
        $dimensiones = $respuestas_perfilacion[0]->respuesta_sel_multiple;
        // Array con los IDs y nombres de las dimensiones (tabla planes_categorias)
        $dimensiones_definidas = PlanesCategoria::get()->toArray();
        // Recorre el array de IDs y obtiene el ID y nombre correspondiente
        $dimensiones_user = [];
        // Filtrar el primer array para encontrar coincidencias con los IDs del segundo array
        $resultado = array_filter($dimensiones_definidas, function ($dimension) use ($dimensiones) {
            return in_array($dimension['perfilacion_respuesta_id'], $dimensiones);
        });
        //Reiniciar las posiciones del arreglo
        $dimensiones_user = array_values($resultado);

        //Capturar el nivel de riesgo que tuvo el usuario en el test - se trae el score del ultimo test 
        /*
        $score = ScoreResult::where('user_id', $user)
            ->orderBy('id', 'desc')
            ->first();
        if (is_null($score)) {
            return response()->json([
                'message' => 'El usuario no cuenta con test para calcular su score.'
            ], 400);
        }
        $nivel_riesgo_user = $score->nivel_riesgo_id;
        */

        //$plan_personalizado = create_misiones($listado_misiones, $dimensiones_user);
        $json_plan_personalizado = create_plan($dimensiones_user);
        //Crear plan
        $plan_user = PlanesPersonalizado::create([
            'user_id' => $user,
            'fecha_inicio' => $fecha_actual,
            'fecha_fin' => Carbon::parse($fecha_actual)->addDays(90),
            'vigente' => '1',
            'por_progreso' => 0,
            'contador_dias' => 0,  //en 0 para que en 1 de la primer misión
            'json_misiones_dimensiones' => $json_plan_personalizado
        ]);
        //Se deben dar 3 misiones al usuario.
        $ids_misiones_ini = [1, 2]; // Reemplaza estos con los IDs específicos que necesitas
        //Seleccionar misiones
        $misiones = ListadoMision::whereIn('id', $ids_misiones_ini)->inRandomOrder()->limit(2)->get()->toArray();
        //Seleccionar submisiones
        for ($i = 0; $i < count($misiones); $i++) { 
            $periodo_id = $misiones[$i]['periodo_id'];
             
            // Mejor definir todas como semanales
            if ($periodo_id == 2) {
                $dias = 8;
            } else if ($periodo_id == 3) {
                $dias = 30;
            }
        
            // Registrar la misión en el plan personalizado
            $misionPlan = PlanesMision::create([
                'plan_personalizado_id' => $plan_user->id, 
                'mision_id' => $misiones[$i]['id'],
                'completado' => 0,
                'fecha_inicio' => $fecha_actual,
                'fecha_fin' => $fecha_actual = Carbon::now()->addDays($dias)->format('Y-m-d'),
                'habilitado' => 1
            ]);
        
            // Seleccionar más submisiones de esa misión
            $submisiones = ListadoSubmision::where('mision_id', $misiones[$i]['id'])->get();
            
            // Registrar la submisión en el plan
            for ($j = 0; $j < count($submisiones); $j++) { // Usamos $j en lugar de $i
                PlanesSubmision::create([
                    'plan_mision_id' => $misionPlan->id, 
                    'submision_id' => $submisiones[$j]->id,
                    'completado' => 0
                ]);
            }
        }
        
        //Guardarlas en el plan de la persona
        return response()->json([
            'message' => 'Plan de dimensiones creado correctamente.'
        ], 200);
    }

    //Cada dia se actualiza el plan con nuevas misiones.
    //Son 4 misiones a la semana, entonces se da una misión diaria hasta el día 4
    public function update($id, Request $request)
    {
        dd("Añadir misiones al plan");
    }

    //Mostrar el plan personalizado completo (misiones, submisiones, progreso)
    public function show_plan(){
        $user = auth()->id();
        $plan = PlanesPersonalizado::where('planes_personalizados.user_id', '=', $user)
            ->join('planes_misiones', 'planes_misiones.plan_personalizado_id', '=', 'planes_personalizados.id')
            ->join('planes_submisiones', 'planes_submisiones.plan_mision_id', '=', 'planes_misiones.id') //MISMO NOMBRE DE ATRIBUTO PERO DIFERENTE RELACION, CAMBIAR
            ->join('listado_misiones', 'listado_misiones.id', '=', 'planes_misiones.mision_id')
            ->join('listado_submisiones', 'listado_submisiones.id', '=', 'planes_submisiones.submision_id')
            ->select(
                'planes_misiones.id AS plan_mision_id',
                'planes_misiones.habilitado',
                'planes_misiones.completado AS completado_mision',
                'planes_misiones.fecha_inicio AS mision_fecha_inicio',
                'planes_misiones.fecha_fin AS mision_fecha_fin',
                'listado_misiones.id AS listado_mision_id',
                'listado_misiones.codigo AS mision_codigo_id',
                'listado_misiones.nombre AS mision_nombre',
                'listado_misiones.descripcion AS mision_descripcion',
                'listado_misiones.puntos AS mision_puntos',
                'planes_submisiones.id AS submision_id',
                'planes_submisiones.completado AS completado_submision',
                'listado_submisiones.id AS listado_submision_id',
                'listado_submisiones.codigo AS submision_codigo',
                'listado_submisiones.nombre AS submision_nombre',
                'listado_submisiones.descripcion AS submision_descripcion',
                'listado_submisiones.nivel AS submision_nivel'
            )
            ->get()
            ->toArray();
        return response()->json($plan, 200);
    }

    //Muestra las misiones, su nivel, icono y orden de secuencia.
    public function ruta_misiones()
    {
        $orden = 0;
        $user = auth()->id();
        //Icono, nivel, ID(ORDEN), Habilitada
        $misiones = PlanesPersonalizado::where('planes_personalizados.user_id', '=', $user)
            ->join('planes_misiones', 'planes_misiones.plan_personalizado_id', '=', 'planes_personalizados.id')
            ->join('listado_misiones', 'listado_misiones.id', '=', 'planes_misiones.mision_id')
            ->select(
                'planes_misiones.id AS plan_mision_id',
                //'listado_misiones.id AS listado_mision_id',
                'listado_misiones.icono',
                'planes_misiones.habilitado'
            )
            ->get()
            ->toArray();
        // Recorrer cada misión y añadir el nivel determinado
        foreach ($misiones as &$mision) {
            $orden++;
            // Reorganizar el array para que 'orden' sea el primer campo
            $mision = array_merge(
                ['orden' => $orden], // Añadir el campo 'orden' primero
                $mision // Luego añadir el resto de la misión
            );

            if ($mision['habilitado'] == 0) {
                $mision['nivel_actual'] = 1;
            } else {
                $nivel = determinar_nivel($mision['plan_mision_id']);
                $mision['nivel_actual'] = $nivel['nivel_actual'];
            }
        }

        // Devolver las misiones con el atributo 'nivel' añadido
        return response()->json($misiones, 200);
    }

    //Habilita las misiones segun la fecha de inicio - Servicio que debe correr cada 24 horas (manual)
    public function habilitar_misiones()
    {
        // Fecha actual
        $fecha_actual = Carbon::now()->format('Y-m-d');
        // Capturar todas las misiones con fecha_inicio igual a la fecha actual
        $misiones = PlanesMision::where('fecha_inicio', '=', $fecha_actual)
            ->where('habilitado', '=', 0)
            ->get();
        // Modificar el atributo habilitado de 0 a 1 en las misiones que cumplen la condición
        foreach ($misiones as $mision) {
            $mision->habilitado = 1;
            $mision->save();
        }
        return response()->json("Ok", 200);
    }

    //Mostrar detalle de una misión - detalles básicos
    public function show_mision($mision_id)
    {
        //Falta inner categoria, periodo, 
        $mision = PlanesMision::where('planes_misiones.id', '=', $mision_id)
            ->join('listado_misiones', 'listado_misiones.id', '=', 'planes_misiones.mision_id')
            ->join('planes_categorias', 'planes_categorias.id', '=', 'listado_misiones.categoria_id')
            ->join('tipo_periodos', 'tipo_periodos.id', '=', 'listado_misiones.periodo_id')
            ->select(
                'planes_misiones.id AS plan_mision_id',
                'planes_misiones.habilitado',
                'planes_misiones.fecha_inicio',
                'planes_misiones.completado',
                'listado_misiones.icono',
                'listado_misiones.codigo',
                'listado_misiones.nombre',
                'listado_misiones.descripcion',
                'listado_misiones.puntos',
                'planes_categorias.nombre AS categoria_nombre',
                'tipo_periodos.nombre AS periodo_nombre'
            )->first();
        return response()->json($mision, 200);
    }

    //Mostrar los detalles de la misión y submisiones
    public function show_mision_submisiones($mision_id)
    {
        //Falta inner categoria, periodo,
        $mision = PlanesMision::where('planes_misiones.id', '=', $mision_id)
            ->join('listado_misiones', 'listado_misiones.id', '=', 'planes_misiones.mision_id')
            ->join('planes_categorias', 'planes_categorias.id', '=', 'listado_misiones.categoria_id')
            ->join('tipo_periodos', 'tipo_periodos.id', '=', 'listado_misiones.periodo_id')
            ->select(
                'planes_misiones.id AS plan_mision_id',
                'planes_misiones.habilitado',
                'planes_misiones.fecha_inicio',
                'planes_misiones.completado',
                'listado_misiones.icono',
                'listado_misiones.codigo',
                'listado_misiones.nombre',
                'listado_misiones.descripcion',
                'listado_misiones.puntos',
                'planes_categorias.nombre AS categoria_nombre',
                'tipo_periodos.nombre AS periodo_nombre'
            )->first();
        $submisiones = PlanesSubmision::where('planes_submisiones.plan_mision_id', '=', $mision->plan_mision_id)
            ->join('listado_submisiones', 'listado_submisiones.id', '=', 'planes_submisiones.submision_id')
            ->select(
                'planes_submisiones.id AS plan_submision_id',
                'planes_submisiones.completado',
                'listado_submisiones.codigo',
                'listado_submisiones.nombre',
                'listado_submisiones.descripcion',
                'listado_submisiones.nivel'
            )->get()
            ->toArray();
        $result = [
            "mision" => $mision,
            "submisiones" => $submisiones
        ];
        return response()->json($result, 200);
    }

    //Listado de misiones semanales
    public function misiones_semanales()
    {
        $user = auth()->id();
        $misiones = PlanesMision::where('planes_personalizados.user_id', '=', $user)
            ->where('tipo_periodos.id', '=', 2) // 2 - SEMANAL
            ->join('planes_personalizados', 'planes_personalizados.id', '=', 'planes_misiones.plan_personalizado_id')
            ->join('listado_misiones', 'listado_misiones.id', '=', 'planes_misiones.mision_id')
            ->join('planes_categorias', 'planes_categorias.id', '=', 'listado_misiones.categoria_id')
            ->join('tipo_periodos', 'tipo_periodos.id', '=', 'listado_misiones.periodo_id')
            ->select(
                'planes_misiones.id AS plan_mision_id',
                'planes_misiones.habilitado',
                'planes_misiones.fecha_inicio',
                'planes_misiones.completado',
                'listado_misiones.icono',
                'listado_misiones.codigo',
                'listado_misiones.nombre',
                'listado_misiones.descripcion',
                'listado_misiones.puntos',
                'planes_categorias.nombre AS categoria_nombre',
                'tipo_periodos.nombre AS periodo_nombre'
            )->get()
            ->toArray();
        for ($i = 0; $i < count($misiones); $i++) {
            //Determinar en que nivel de submisiones esta la misión
            $nivel = PlanesSubmision::where('planes_submisiones.plan_mision_id', '=', $misiones[$i]['plan_mision_id'])
                ->where('planes_submisiones.completado', '=', 1)
                ->join('listado_submisiones', 'listado_submisiones.id', '=', 'planes_submisiones.submision_id')
                ->select('*')
                ->count();

            // Botones según el número de submisiones completadas:
            // 0 submisiones: botón '+'
            // 1 submisión: botón '1'
            // 2 submisiones: botón '2'
            // 3 submisiones: botón '3'
            // 4 o más: botón 'check'
            $botones = [
                0 => 'boton_+',
                1 => 'boton_1',
                2 => 'boton_2',
                3 => 'boton_3'
            ];
            $progresos = [
                0 => '0',
                1 => '25',
                2 => '50',
                3 => '75'
            ];
            // Si el nivel existe en el arreglo, asigna el botón correspondiente, de lo contrario asigna 'boton_check'
            $boton = $botones[$nivel] ?? 'boton_check';
            $progreso_mision = $progresos[$nivel] ?? '100';
            //Añadir el campo botón al arreglo del elemento.
            $misiones[$i]['boton'] = $boton;
            $misiones[$i]['progreso_mision'] = $progreso_mision;
        }
        return response()->json($misiones, 200);
    }

    //Listado de misiones mensuales
    public function misiones_mensuales()
    {
        $user = auth()->id();
        $misiones = PlanesMision::where('planes_personalizados.user_id', '=', $user)
            ->where('tipo_periodos.id', '=', 3) // 3 - MENSUAL
            ->join('planes_personalizados', 'planes_personalizados.id', '=', 'planes_misiones.plan_personalizado_id')
            ->join('listado_misiones', 'listado_misiones.id', '=', 'planes_misiones.mision_id')
            ->join('planes_categorias', 'planes_categorias.id', '=', 'listado_misiones.categoria_id')
            ->join('tipo_periodos', 'tipo_periodos.id', '=', 'listado_misiones.periodo_id')
            ->select(
                'planes_misiones.id AS plan_mision_id',
                'planes_misiones.habilitado',
                'planes_misiones.fecha_inicio',
                'planes_misiones.completado',
                'listado_misiones.icono',
                'listado_misiones.codigo',
                'listado_misiones.nombre',
                'listado_misiones.descripcion',
                'listado_misiones.puntos',
                'planes_categorias.nombre AS categoria_nombre',
                'tipo_periodos.nombre AS periodo_nombre'
            )->get()
            ->toArray();

        for ($i = 0; $i < count($misiones); $i++) {
            //Determinar en que nivel de submisiones esta la misión
            $nivel = PlanesSubmision::where('planes_submisiones.plan_mision_id', '=', $misiones[$i]['plan_mision_id'])
                ->where('planes_submisiones.completado', '=', 1)
                ->join('listado_submisiones', 'listado_submisiones.id', '=', 'planes_submisiones.submision_id')
                ->select('*')
                ->count();

            // Botones según el número de submisiones completadas:
            // 0 submisiones: botón '+'
            // 1 submisión: botón '1'
            // 2 submisiones: botón '2'
            // 3 submisiones: botón '3'
            // 4 o más: botón 'check'
            $botones = [
                0 => 'boton_+',
                1 => 'boton_1',
                2 => 'boton_2',
                3 => 'boton_3'
            ];
            $progresos = [
                0 => '0',
                1 => '25',
                2 => '50',
                3 => '75'
            ];
            // Si el nivel existe en el arreglo, asigna el botón correspondiente, de lo contrario asigna 'boton_check'
            $boton = $botones[$nivel] ?? 'boton_check';
            $progreso_mision = $progresos[$nivel] ?? '100';
            //Añadir el campo botón al arreglo del elemento.
            $misiones[$i]['boton'] = $boton;
            $misiones[$i]['progreso_mision'] = $progreso_mision;
        }

        return response()->json($misiones, 200);
    }

    //Mostrar submisiones de una misión
    public function show_submisiones($mision_id)
    {
        $submisiones = PlanesSubmision::where('planes_submisiones.plan_mision_id', '=', $mision_id)
            ->join('listado_submisiones', 'listado_submisiones.id', '=', 'planes_submisiones.submision_id')
            ->select(
                'planes_submisiones.id AS plan_submision_id',
                'planes_submisiones.completado',
                'listado_submisiones.codigo',
                'listado_submisiones.nombre',
                'listado_submisiones.descripcion',
                'listado_submisiones.nivel'
            )->get()
            ->toArray();
        $result = [
            "submisiones" => $submisiones
        ];
        return response()->json($result, 200);
    }

    //Añadir progreso a la submisión (completarla)
    public function edit_submision($id, Request $request)
    {
        $submision = PlanesSubmision::where('id', '=', $id)->first();
        $submision->completado = 1;
        $submision->save();
        return response()->json("Ok", 200);
    }

    public function generar_mision(Request $request)
    {
        //Con el id del usuario se selecciona su plan actual
        /*

 */
        //Validacion_asignarMision($usuario_asignacion, $tipoMision, $tipo_usuario)
        dd("Ok");
    }

    public function reemplazar_mision($id, Request $request)
    {
        dd("Ok");
    }

    public function progreso_full()
    {
        $user = auth()->id();
        //% De progreso del plan personalizado
        $progreso = medir_progreso_plan($user);
        //Días de racha
        $racha_actual = 0;
        $racha = Racha::where('user_id', $user)->first();
        if ($racha) {
            $racha_actual = $racha->racha_actual;
        }
        //Cantidad puntos
        $puntos_actual = 0;
        $puntos = UserPunto::where('user_id', $user)->first();
        if ($puntos) {
            $puntos_actual = $puntos->total_puntos;
        }
        //Logros
        $logros = UserLogro::where('user_id', $user)->count();
        $result = [
            "progreso" => $progreso . "%",
            "racha" => $racha_actual,
            "puntos" => $puntos_actual,
            "logros" => $logros
        ];
        return response()->json($result, 200);
    }
}

//% completo del plan
function medir_progreso_plan($user)
{
    //Se calcula mejor con sumbisiones, porque hay misiones que tienen diferentes niveles, entonces queda mejor calculado
    $submisiones_completadas = PlanesPersonalizado::join('planes_misiones', 'planes_misiones.plan_personalizado_id', '=', 'planes_personalizados.id')
        ->join('planes_submisiones', 'planes_submisiones.plan_mision_id', '=', 'planes_misiones.id')
        ->where('planes_submisiones.completado', 1)
        ->where('planes_personalizados.user_id', $user)
        ->count();
    $sumbisiones_totales = PlanesPersonalizado::join('planes_misiones', 'planes_misiones.plan_personalizado_id', '=', 'planes_personalizados.id')
        ->join('planes_submisiones', 'planes_submisiones.plan_mision_id', '=', 'planes_misiones.id')
        ->where('planes_personalizados.user_id', $user)
        ->count();
    if ($sumbisiones_totales > 0) {
        $porcentaje_completado = ($submisiones_completadas / $sumbisiones_totales) * 100;
    } else {
        $porcentaje_completado = 0;
    }
    return $porcentaje_completado;
}

//Determina en que nivel se encuentra la misión actual.
function determinar_nivel($plan_mision_id)
{
    // Obtener el último nivel completado
    $submisiones = PlanesSubmision::where('planes_submisiones.plan_mision_id', '=', $plan_mision_id)
        ->where('planes_submisiones.completado', '=', 1)
        ->join('listado_submisiones', 'listado_submisiones.id', '=', 'planes_submisiones.submision_id')
        ->select(
            'planes_submisiones.id AS plan_submision_id',
            'listado_submisiones.id AS listado_submision_id',
            'listado_submisiones.nivel',
            'planes_submisiones.completado'
        )
        ->orderBy('listado_submisiones.nivel', 'desc')
        ->first();

    // Si no hay misiones completadas, el nivel actual es el primero
    if (is_null($submisiones)) {
        $nivel_actual = 1;
        //$estado = 'iniciado'; // Indica que acaba de comenzar el nivel
    }
    // Si el último nivel completado es menor a 4, avanzar al siguiente nivel
    else if ($submisiones->nivel >= 1 && $submisiones->nivel < 4) {
        $nivel_actual = $submisiones->nivel + 1;
        //$estado = 'iniciado'; // Indica que acaba de iniciar este nivel
    }
    // Si el último nivel completado es 4
    else if ($submisiones->nivel == 4) {
        $nivel_actual = 'completado';
        //$estado = 'completado'; // El nivel 4 ya fue completado
    }

    // Devolver el nivel actual y su estado
    return [
        'nivel_actual' => $nivel_actual,
        //'estado' => $estado // "iniciado", "en curso" o "completado"
    ];
}

//Determina la validación si un usuario puede generar o no retos esa semana, verifica su limite.
function Validacion_asignarMision($usuario_asignacion, $tipoMision, $tipo_usuario)
{
    //validar el tipo de usuario, si es pro, flex, full
    $hoy = Carbon::now();
    // Para misiones mensuales
    if ($tipoMision === 'mensual') {
        $fechaUltimaMision = $usuario_asignacion->ultima_asignacion_mensual;
        // Verifica si no hay fecha de última misión o si ha pasado más de un mes
        if (!$fechaUltimaMision || $hoy->diffInMonths($fechaUltimaMision) >= 1) {
            // Reinicia el contador de misiones mensuales porque ha pasado un mes
            $usuario_asignacion->misiones_mensuales = 0;
            $usuario_asignacion->ultima_asignacion_mensual = $hoy;
        }
        // Verifica si ya ha alcanzado el límite de misiones mensuales en el mes actual
        if ($usuario_asignacion->misiones_mensuales >= 2) {
            return response()->json(['validacion' => false]);
        }
        // Asignar misión mensual
        $usuario_asignacion->misiones_mensuales++;
        $usuario_asignacion->ultima_asignacion_mensual = $hoy; // Actualiza la fecha de asignación
        $usuario_asignacion->save();
        return response()->json(['validacion' => true]);
    }

    // Para misiones semanales
    if ($tipoMision === 'semanal') {
        $fechaUltimaMision = $usuario_asignacion->ultima_asignacion_semanal;
        // Verifica si no hay fecha de última misión o si ha pasado más de una semana
        if (!$fechaUltimaMision || $hoy->diffInWeeks($fechaUltimaMision) >= 1) {
            // Reinicia el contador de misiones semanales porque ha pasado una semana
            $usuario_asignacion->misiones_semanales = 0;
            $usuario_asignacion->ultima_asignacion_semanal = $hoy;
        }
        // Verifica si ya ha alcanzado el límite de misiones semanales (máximo 3)
        if ($usuario_asignacion->misiones_semanales >= 3) {
            return response()->json(['validacion' => false]);
        }
        // Asignar misión semanal
        $usuario_asignacion->misiones_semanales++;
        $usuario_asignacion->ultima_asignacion_semanal = $hoy; // Actualiza la fecha de asignación
        $usuario_asignacion->save();
        return response()->json(['validacion' => true]);
    }
    return response()->json(['validacion' => false]);
}

//Crea las misiones iniciales del plan personalizado.
function create_misiones($listado_misiones, $dimensiones_user)
{
    $response = false;
    // Contar cuántas dimensiones se tienen (máximo 8)
    $cantidad_dimensiones = count($dimensiones_user); // Ajusta este valor según lo que obtengas de las dimensiones
    $cantidad_misiones = 6; // Total de misiones
    $misiones_semanales = 4; // Número de misiones semanales
    $misiones_mensuales = 2; // Número de misiones mensuales

    // Calcular misiones por dimensión
    if ($cantidad_dimensiones <= 6) {
        $misiones_por_cat = intdiv($cantidad_misiones, $cantidad_dimensiones); // Número base de misiones por dimensión
        $misiones_extra = $cantidad_misiones % $cantidad_dimensiones; // Misiones que sobran para distribuir
    } else {
        // Si hay más de 6 dimensiones, limitamos a 1 misión por cada dimensión
        $misiones_por_cat = 1;
        $misiones_extra = 0;
    }
    // Array para almacenar el plan de misiones
    $plan_misiones = [
        'semanales' => [],
        'mensuales' => []
    ];

    foreach ($dimensiones_user as $index => $dimension) {
        // Asignamos el número base de misiones a esta dimensión
        $cantidad_misiones_asignadas = $misiones_por_cat;
        // Distribuir misiones extra en las primeras dimensiones
        if ($misiones_extra > 0) {
            $cantidad_misiones_asignadas += 1;
            $misiones_extra--;
        }

        // Seleccionamos las misiones de la dimensión actual
        $misiones = ListadoMision::where('categoria_id', $dimension['id'])
        ->inRandomOrder() // Selección aleatoria
        ->take($cantidad_misiones_asignadas) // Cantidad de misiones a asignar para esta dimensión
        ->get()
        ->toArray();

        //Validar que tenga datos la consulta para poder clasificarla.
        if (count($misiones) > 0) {
            // Clasificamos las misiones en semanales y mensuales según 'periodo_id'
            foreach ($misiones as $mision) {
                if ($mision['periodo_id'] == 2) {
                    // Si 'periodo_id' es 2, clasificar como misión semanal
                    $plan_misiones['semanales'][] = $mision;
                } elseif ($mision['periodo_id'] == 3) {
                    // Si 'periodo_id' es 3, clasificar como misión mensual
                    $plan_misiones['mensuales'][] = $mision;
                }
            }
        }
    }
    // Asegurar que el total de misiones sea 6 (4 semanales y 2 mensuales)
    $plan_misiones['semanales'] = array_slice($plan_misiones['semanales'], 0, $misiones_semanales);
    $plan_misiones['mensuales'] = array_slice($plan_misiones['mensuales'], 0, $misiones_mensuales);
    $response = true;
    //retornar true o false, en caso de que se hay creado el plan, y el listado de misiones
    return [
        'response' => $response,         // Asigna el nombre 'response' al primer valor
        'misiones' => $plan_misiones // Asigna el nombre 'plan_misiones' al segundo valor
    ];
}

function create_plan($dimensiones_user) {
    // 36 misiones semanales y 12 misiones mensuales
    $dimensiones_totales = count($dimensiones_user); // Número de dimensiones seleccionadas por el usuario
    $semanas_totales = 12; // Total de semanas (3 meses)
    $mision_semanal_id = 2;
    $mision_mensual_id = 3;
    
    // Armar el plan de misiones
    $misiones = [];
    $dia = 1; // Día inicial
    $dimension_index = 0; // Índice cíclico para dimensiones
    
    for ($semana = 1; $semana <= $semanas_totales; $semana++) {
        // Misiones semanales (2 primeras de la semana)
        for ($i = 1; $i <= 2; $i++) {
            $dimension_id = $dimensiones_user[$dimension_index]['id'];
            
            $misiones[] = [
                "nombre" => "Misión semanal $semana.$i",
                "periodo_id" => $mision_semanal_id,
                "dia" => $dia,
                "dimension_id" => $dimension_id
            ];

            $dimension_index = ($dimension_index + 1) % $dimensiones_totales;
            $dia += 2; // Incrementamos el día en 2
        }

        // Última misión semanal de la semana
        $dimension_id = $dimensiones_user[$dimension_index]['id'];
        $misiones[] = [
            "nombre" => "Misión semanal $semana.3",
            "periodo_id" => $mision_semanal_id,
            "dia" => $dia,
            "dimension_id" => $dimension_id
        ];
        
        $dimension_index = ($dimension_index + 1) % $dimensiones_totales;

        // Incrementamos el día en 2 para la próxima semana
        $dia += 2;

        // Misión mensual después de la última misión semanal
        $dimension_id = $dimensiones_user[$dimension_index]['id'];
        $misiones[] = [
            "nombre" => "Misión mensual $semana",
            "periodo_id" => $mision_mensual_id,
            "dia" => $dia,
            "dimension_id" => $dimension_id
        ];
        
        $dimension_index = ($dimension_index + 1) % $dimensiones_totales;

        // Incrementamos el día en 2 para la próxima misión semanal
        $dia += 2;
    }
    
    // Ordenamos las misiones por día para mantener el orden
    usort($misiones, function($a, $b) {
        return $a['dia'] - $b['dia'];
    });

    // Convertimos el array de misiones a JSON
    header('Content-Type: application/json');
    $json = json_encode($misiones, JSON_UNESCAPED_UNICODE);
    $json = str_replace("\n", "", $json); // Elimina los saltos de línea
    return $json;
}

