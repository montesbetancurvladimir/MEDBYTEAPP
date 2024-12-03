<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\PlanesMision;

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccionRealizada
{
    use Dispatchable, SerializesModels;

    public $user;
    public $accion;
    public $detalle;  // Detalles adicionales, como id de misión, tipo de nota, etc.

    public function __construct(User $user, $accion, $detalle = null)
    {
        $this->user = $user;
        $this->accion = $accion;
        $this->detalle = $detalle;  // Información extra relevante para la acción
    }
}
