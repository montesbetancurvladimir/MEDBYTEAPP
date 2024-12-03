<?php

namespace App\Console\Commands;

use App\Models\ListadoMision;
use App\Models\ListadoSubmision;
use App\Models\PlanesMision;
use App\Models\PlanesSubmision;
use Illuminate\Console\Command;
use App\Models\PlanesPersonalizado;
use Carbon\Carbon;
//  php artisan app:plan-personalizado-nuevas-misiones (probar la tarea programada )

class PlanPersonalizadoNuevasMisiones extends Command
{
    protected $signature = 'app:plan-personalizado-nuevas-misiones';
    protected $description = 'Revisa los días en los que van los planes de cada usuario, con el objetivo de asignar misiones';
    public function __construct(){
        parent::__construct();
    }
    public function handle(){
        $fecha_actual = Carbon::now()->format('Y-m-d');
        //Capturar aquellos planes vigentes y que no sea null el json 
        $registros = PlanesPersonalizado::where('vigente', 1)->whereNotNull('json_misiones_dimensiones')->get();
        dd($registros);
        //Coger cada plan y verificar en que día se encuentra
        foreach ($registros as $plan) {
            $dia_plan = $plan->contador_dias;
            $json_dimensiones = json_decode($plan->json_misiones_dimensiones, true);
            // Filtrar las misiones correspondientes al día del plan
            $misionesDelDia = array_filter($json_dimensiones, function($mision) use ($dia_plan) {
                return $mision['dia'] == $dia_plan;
            });
            dd($json_dimensiones);
            //si es vacio, no tiene misiones ese día, si tiene registro tiene misiones 
            if(isset($misionesDelDia)){
                //Extrae el primer elemento, porque hay una posicion que me estorba
                $firstElement = reset($misionesDelDia);
                //Extrae la dimension asignada y periodo
                $dimension_id = $firstElement['dimension_id'];
                $periodo_id = $firstElement['periodo_id'];

                //Trae una mision aleatoria de esa categoria.
                $misionesAsignadas = PlanesMision::where('user_id', auth()->id())
                ->pluck('mision_id');  // Extrae los IDs de las misiones asignadas al usuario
                dd($misionesAsignadas);

                $mision = ListadoMision::where('periodo_id', $periodo_id)
                ->where('categoria_id', $dimension_id)
                ->whereNotIn('id', $misionesAsignadas)  // Excluir misiones asignadas
                ->inRandomOrder()  // Ordenar aleatoriamente
                ->first();


                /* 
                $mision = ListadoMision::where('periodo_id', $periodo_id)
                    ->where('categoria_id', $dimension_id)
                    ->inRandomOrder()  // Ordenar aleatoriamente
                    ->first();
                */


                //Trae sus submisiones y las registra tambien
                $submisiones = ListadoSubmision::where('mision_id', $mision->id)
                ->inRandomOrder()  // Ordenar aleatoriamente
                ->get();
                //Hace el registro en el plan del usuario.
                if($periodo_id == 2){
                    $dias = 8;
                }else if($periodo_id == 3){
                    $dias = 30;
                }
                $misionPlan = PlanesMision::create([
                    'plan_personalizado_id' => $plan->id, 
                    'mision_id' => $mision->id,
                    'completado' => 0,
                    'fecha_inicio' => $fecha_actual,
                    'fecha_fin' => $fecha_actual = Carbon::now()->addDays($dias)->format('Y-m-d'),
                    'habilitado' => 1,
                    'vigente' => 1
                ]);
                for ($i=0; $i < count($submisiones); $i++) { 
                    PlanesSubmision::create([
                        'plan_mision_id' => $misionPlan, 
                        'submision_id' => $submisiones[$i]['id'],
                        'completado' => 0
                    ]);
                }

            }
        }
    }
}
