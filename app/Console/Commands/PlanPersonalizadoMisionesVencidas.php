<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ListadoMision;
use App\Models\ListadoSubmision;
use App\Models\PlanesMision;
use App\Models\PlanesSubmision;
use App\Models\PlanesPersonalizado;
use Carbon\Carbon;

class PlanPersonalizadoMisionesVencidas extends Command
{

    protected $signature = 'app:plan-personalizado-misiones-vencidas';
    protected $description = 'Misiones que ya pasó su fecha de vencimiento';
    public function __construct(){
        parent::__construct();
    }
    public function handle(){
        //Capturar las misiones con fecha_fin >= a fecha actual
        $fecha_actual = Carbon::now()->format('Y-m-d');
        $misiones_vencidas = ListadoMision::where('fecha_fin','>=', $fecha_actual)
        ->where('habilitado', 1)->get();
        foreach ($misiones_vencidas as $mision) {
            $mision->habilitado = 0;
            $mision->save();
        }

    }
}
