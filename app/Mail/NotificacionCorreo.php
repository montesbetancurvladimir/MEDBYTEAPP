<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionCorreo extends Mailable
{
    use Queueable, SerializesModels;
    public $mensaje;
    public $correo_nuevo;

    public function __construct($mensaje,$correo_nuevo){
        $this->mensaje = $mensaje;
        $this->correo_nuevo = $correo_nuevo;
    }

    public function build()
    {
        //Validar cual fue el correo que se registró o el ultimo que completo el test para añadirlo para enviar.
        return $this->from(env('MAIL_FROM_ADDRESS'))
                    ->subject('Notificación Registro')
                    ->view('mails.notificacion')
                    ->with([
                        'mensaje' => $this->mensaje,
                        'correo_nuevo' => $this->correo_nuevo
                    ]);
    }
}
