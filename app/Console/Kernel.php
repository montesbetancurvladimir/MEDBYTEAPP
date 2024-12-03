<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    //everyMinute - daily
    protected function schedule(Schedule $schedule): void{
        $schedule->command('app:contador-misiones')->everyMinute(); //Contador de misiones de los usuarios 
    }

    protected function commands(): void{
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
