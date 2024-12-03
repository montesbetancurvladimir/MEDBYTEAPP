<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\AccionRealizada;
use App\Services\LogroService;

class VerificarLogros
{
    protected $logroService;

    public function __construct(LogroService $logroService)
    {
        $this->logroService = $logroService;
    }

    public function handle(AccionRealizada $event)
    {
        $this->logroService->verificarYOtorgarLogros($event->user, $event->accion, $event->detalle);
    }
}
