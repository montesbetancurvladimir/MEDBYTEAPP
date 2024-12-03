<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PlanesPersonalizado;

//php artisan make:command IncrementCounter CREARLO
//Registrarlo en el Kernel
// php artisan app:contador-misiones (probar la tarea programada )

class ContadorMisiones extends Command
{
    protected $signature = 'app:contador-misiones';
    protected $description = 'Tarea programada para actualizar cada 24 horas el día en el que el usuario se encuentra en el plan personalizado y cerrar los planes de 91 dìas';
    //91 días porque el 90 es el último y pueden hacer misiones, al otro día cuando se actualice finalizaría
    public function __construct(){
        parent::__construct();
    }

    public function handle(){
        // Trae todos los planes personalizados vigentes y que tengan el json construido
        $registros = PlanesPersonalizado::where('vigente', 1)
        ->whereNotNull('json_misiones_dimensiones')
        ->get();
        // A cada registro le suma 1 en el contador
        foreach ($registros as $registro) {
            $registro->contador_dias += 1;
            $registro->save();
        }
        //Los planes que superaron el periodo de 90 días, pasan a ser inactivados
        $planes_vencidos = PlanesPersonalizado::where('contador_dias', '>=', 91)->get();
        foreach ($planes_vencidos as $plan) {
            $registro->vigente = 0;
            $registro->save();
        }
        $this->info('Contadores incrementados exitosamente.');
    }

}
